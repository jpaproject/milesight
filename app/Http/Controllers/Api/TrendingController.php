<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceReading;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrendingController extends Controller
{
    /**
     * GET /api/trending
     *
     * Returns time-series data grouped by device for charting.
     * Supports filters: metric (temperature|humidity|battery), device_id, start_date, end_date.
     * Defaults to today's temperature data when no filter is provided.
     *
     * Response format follows INTEGRATION_SOP.md §1 (envelope: success, message, data).
     */
    public function index(Request $request): JsonResponse
    {
        $metric = $request->query('metric', 'temperature');
        $allowedMetrics = ['temperature', 'humidity', 'battery'];
        if (!in_array($metric, $allowedMetrics, true)) {
            $metric = 'temperature';
        }

        $query = DeviceReading::with('device:id,name');

        // Filter by specific device
        if ($request->filled('device_id') && $request->device_id !== 'all') {
            $query->where('device_id', $request->device_id);
        }

        $hasDateFilter =
            $request->filled('start_date') ||
            $request->filled('end_date');

        if ($request->filled('start_date')) {
            $query->whereDate('received_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('received_at', '<=', $request->end_date);
        }

        // Default to today if no date filter provided
        if (!$hasDateFilter) {
            $query->whereDate('received_at', Carbon::today());
        }

        $readings = $query
            ->select('id', 'device_id', $metric, 'received_at')
            ->orderBy('received_at')
            ->get();

        // Max data points per device — keeps JSON payload small for mobile
        $maxPointsPerDevice = 100;

        $series = $readings
            ->groupBy('device_id')
            ->map(function ($rows) use ($metric, $maxPointsPerDevice) {
                $deviceName = optional($rows->first()->device)->name ?? 'Unknown Device';
                $deviceId = $rows->first()->device_id;

                // Sample evenly if too many data points
                $total = $rows->count();
                if ($total > $maxPointsPerDevice) {
                    $step = $total / $maxPointsPerDevice;
                    $sampled = collect();
                    for ($i = 0; $i < $maxPointsPerDevice; $i++) {
                        $sampled->push($rows[min((int) round($i * $step), $total - 1)]);
                    }
                    $rows = $sampled;
                }

                $data = $rows->map(function ($row) use ($metric) {
                    return [
                        'x' => $row->received_at->getTimestamp() * 1000,
                        'y' => (float) $row->{$metric},
                    ];
                })->values();

                return [
                    'device_id' => $deviceId,
                    'name'      => $deviceName,
                    'data'      => $data,
                ];
            })
            ->values();

        $metricLabel = match ($metric) {
            'humidity'    => 'Humidity',
            'battery'     => 'Battery',
            default       => 'Temperature',
        };

        return response()->json([
            'success' => true,
            'message' => 'Trending data retrieved.',
            'data'    => [
                'metric'        => $metric,
                'metric_label'  => $metricLabel,
                'showing_today' => !$hasDateFilter,
                'total_records' => $readings->count(),
                'series'        => $series,
            ],
        ]);
    }
}
