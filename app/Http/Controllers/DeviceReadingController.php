<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceReadingRequest​;
use App\Models\Area;
use App\Models\Device;
use App\Models\DeviceReading;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeviceReadingController extends Controller
{
    public function index(Request $request)
    {
        $query = DeviceReading::with(['area', 'device']);

        $areaId = $request->query('area_id');
        if ($request->filled('area_id') && $areaId !== 'all') {
            $query->whereHas('area', function ($q) use ($areaId) {
                $q->where('name', $areaId);
            });
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

        if (! $hasDateFilter) {
            $query->whereDate('received_at', Carbon::today());
            $showingToday = true;
        } else {
            $showingToday = false;
        }

        $logs = $query->orderBy('received_at', 'desc')->get();
        $areas = Area::with('terminal')->get();

        if ($request->ajax()) {
            try {
                $html = view('partials.logs-table-rows', compact('logs'))->render();

                return response()->json([
                    'success' => true,
                    'html' => $html,
                    'count' => $logs->count(),
                    'message' => 'Filters applied successfully'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error applying filters: ' . $e->getMessage()
                ], 500);
            }
        }

        return view('pages.logs.index', compact('logs', 'areas', 'showingToday'));
    }



    public function filter(Request $request)
    {
        $query = DeviceReading::with(['area', 'device']);

        // Apply filters
        if ($request->filled('area')) {
            $query->whereHas('area', function ($q) use ($request) {
                $q->where('name', $request->area);
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('received_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('received_at', '<=', $request->end_date);
        }

        if ($request->filled('period')) {
            $now = Carbon::now();

            if ($request->period === 'daily') {
                $query->whereDate('received_at', $now->toDateString());
            } elseif ($request->period === 'monthly') {
                $query->whereMonth('received_at', $now->month)
                    ->whereYear('received_at', $now->year);
            }
        }

        $logs = $query->orderBy('received_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $logs->map(function ($log, $index) {
                return [
                    'no' => $index + 1,
                    'area_name' => $log->area->name,
                    'device_name' => $log->device->name,
                    'battery' => $log->battery,
                    'temperature' => $log->temperature,
                    'humidity' => $log->humidity,
                    'received_at' => Carbon::parse($log->received_at)->format('d/m/Y H:i:s'),
                    'received_at_raw' => $log->received_at
                ];
            })
        ]);
    }

    public function store(StoreDeviceReadingRequest​ $request): JsonResponse
    {
        $timestamp = now();
        $validatedData = $request->validated();

        try {
            $deviceNames = collect($validatedData)->pluck('deviceName')->unique()->all();

            $devices = Device::whereIn('name', $deviceNames)
                ->get()
                ->keyBy('name');

            $sensorDataArray = collect($validatedData)->map(function ($item) use ($devices, $timestamp) {
                $device = $devices[$item['deviceName']] ?? null;

                if (!$device) {
                    return null;
                }

                return [
                    'area_id' => $device->area_id,
                    'device_id' => $device->id,
                    'temperature' => $item['temperature'] ?? null,
                    'humidity' => $item['humidity'] ?? null,
                    'battery' => $item['battery'] ?? null,
                    'received_at' => $timestamp,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            })->filter()
                ->values()
                ->all();

            if (empty($sensorDataArray)) {
                Log::warning('No valid device readings to store.', [
                    'submitted_count' => count($validatedData),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'No valid device readings found',
                ], 422);
            }

            DB::table('device_readings')->insert($sensorDataArray);

            Log::info('Device data stored successfully.', [
                'inserted_count' => count($sensorDataArray),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sensor data stored successfully',
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Failed to store sensor data.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to store sensor data',
                'error' => app()->isLocal() ? $e->getMessage() : 'Internal Server Error',
            ], 500);
        }
    }
}
