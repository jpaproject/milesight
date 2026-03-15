<?php

namespace App\Http\Controllers;

use App\Models\DeviceReading;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrendingController extends Controller
{
    public function index(Request $request)
    {
        $metric = $request->query('metric', 'temperature');
        $allowedMetrics = ['temperature', 'humidity', 'battery'];
        if (!in_array($metric, $allowedMetrics, true)) {
            $metric = 'temperature';
        }

        $query = DeviceReading::with('device');

        $hasDateFilter =
            $request->filled('start_date') ||
            $request->filled('end_date');

        if ($request->filled('start_date')) {
            $query->whereDate('received_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('received_at', '<=', $request->end_date);
        }

        if (!$hasDateFilter) {
            $query->whereDate('received_at', Carbon::today());
            $showingToday = true;
        } else {
            $showingToday = false;
        }

        $readings = $query->orderBy('received_at')->get();

        $series = $readings
            ->groupBy('device_id')
            ->map(function ($rows) use ($metric) {
                $deviceName = optional($rows->first()->device)->name ?? 'Unknown Device';
                $data = $rows->map(function ($row) use ($metric) {
                    return [
                        'x' => $row->received_at->getTimestamp() * 1000,
                        'y' => (float) $row->{$metric},
                    ];
                })->values();

                return [
                    'name' => $deviceName,
                    'data' => $data,
                ];
            })
            ->values();

        $metricLabel = match ($metric) {
            'humidity' => 'Humidity',
            'battery' => 'Battery',
            default => 'Temperature',
        };

        return view('pages.trending.index', [
            'series' => $series,
            'metric' => $metric,
            'metricLabel' => $metricLabel,
            'showingToday' => $showingToday,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'totalRecords' => $readings->count(),
        ]);
    }
}
