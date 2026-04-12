<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceReading;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    /**
     * GET /api/logs
     *
     * Returns paginated device readings for the mobile app.
     * Supports filters: device_id, start_date, end_date, search.
     * Defaults to today's data when no date filter is provided.
     *
     * Response format follows INTEGRATION_SOP.md §2 (Laravel ->paginate()).
     */
    public function index(Request $request): JsonResponse
    {
        $query = DeviceReading::with('device:id,name');

        // Filter by specific device
        if ($request->filled('device_id') && $request->device_id !== 'all') {
            $query->where('device_id', $request->device_id);
        }

        // Search across multiple fields (case insensitive)
        if ($request->filled('search')) {
            $searchTerm = strtolower($request->search);
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('device', function ($sub) use ($searchTerm) {
                    $sub->whereRaw('LOWER(name) LIKE ?', ['%' . $searchTerm . '%']);
                })
                ->orWhereRaw('CAST(CAST(battery AS REAL) AS TEXT) LIKE ?', ['%' . $searchTerm . '%'])
                ->orWhereRaw('CAST(CAST(temperature AS REAL) AS TEXT) LIKE ?', ['%' . $searchTerm . '%'])
                ->orWhereRaw('CAST(CAST(humidity AS REAL) AS TEXT) LIKE ?', ['%' . $searchTerm . '%'])
                ->orWhereRaw("LOWER(TO_CHAR(received_at, 'YYYY-MM-DD HH24:MI:SS')) LIKE ?", ['%' . $searchTerm . '%']);
            });
        }

        // Date range filtering
        $hasDateFilter = $request->filled('start_date') || $request->filled('end_date');

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

        $logs = $query->orderBy('received_at', 'desc')
                      ->paginate($request->input('per_page', 20));

        // Transform data to include device_name at root level
        $logs->getCollection()->transform(function ($reading) {
            return [
                'id'           => $reading->id,
                'device_name'  => $reading->device->name ?? 'Unknown',
                'battery'      => $reading->battery,
                'temperature'  => $reading->temperature,
                'humidity'     => $reading->humidity,
                'received_at'  => $reading->received_at
                    ? Carbon::parse($reading->received_at)->format('Y-m-d H:i:s')
                    : null,
            ];
        });

        return response()->json($logs);
    }

    /**
     * GET /api/logs/devices
     *
     * Returns the list of all devices for the filter dropdown.
     */
    public function devices(): JsonResponse
    {
        $devices = Device::orderBy('name')
                         ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'message' => 'Device list retrieved.',
            'data'    => $devices,
        ]);
    }

    public function export(Request $request)
    {
        $query = DeviceReading::with('device:id,name');

        if ($request->filled('start_date')) {
            $query->whereDate('received_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('received_at', '<=', $request->end_date);
        }

        $fileName = "ThermIQ_Logs_" . now()->format('Ymd_His') . ".xls";

        $headers = [
            "Content-Type"        => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($query) {
            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
            echo ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
            echo ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
            echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
            echo ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
            echo ' <Worksheet ss:Name="Sensor Logs">' . "\n";
            echo '  <Table>' . "\n";

            // Headers
            echo '   <Row>' . "\n";
            $columns = ['Device Name', 'Battery', 'Temperature', 'Humidity', 'Received At'];
            foreach ($columns as $col) {
                echo '    <Cell><Data ss:Type="String">' . htmlspecialchars($col) . '</Data></Cell>' . "\n";
            }
            echo '   </Row>' . "\n";

            $query->orderBy('received_at', 'desc')->chunk(500, function ($readings) {
                foreach ($readings as $reading) {
                    echo '   <Row>' . "\n";
                    echo '    <Cell><Data ss:Type="String">' . htmlspecialchars($reading->device ? $reading->device->name : 'Unknown') . '</Data></Cell>' . "\n";
                    echo '    <Cell><Data ss:Type="String">' . ($reading->battery !== null ? $reading->battery . '%' : '-') . '</Data></Cell>' . "\n";
                    echo '    <Cell><Data ss:Type="String">' . ($reading->temperature !== null ? $reading->temperature . '°C' : '-') . '</Data></Cell>' . "\n";
                    echo '    <Cell><Data ss:Type="String">' . ($reading->humidity !== null ? $reading->humidity . '%' : '-') . '</Data></Cell>' . "\n";
                    echo '    <Cell><Data ss:Type="String">' . ($reading->received_at ? Carbon::parse($reading->received_at)->format('Y-m-d H:i:s') : '-') . '</Data></Cell>' . "\n";
                    echo '   </Row>' . "\n";
                }
            });

            echo '  </Table>' . "\n";
            echo ' </Worksheet>' . "\n";
            echo '</Workbook>' . "\n";
        };

        return response()->stream($callback, 200, $headers);
    }
}
