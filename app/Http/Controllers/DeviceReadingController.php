<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceReadingRequest​;
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
        $query = DeviceReading::with('device');

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

        if (! $hasDateFilter) {
            $query->whereDate('received_at', Carbon::today());
            $showingToday = true;
        } else {
            $showingToday = false;
        }

        $logs = $query->orderBy('received_at', 'desc')->get();
        $devices = Device::orderBy('name')->get(['id', 'name']);
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

        return view('pages.logs.index', compact('logs', 'devices', 'showingToday'));
    }



    public function filter(Request $request)
    {
        $query = DeviceReading::with('device');

        if ($request->filled('device_id') && $request->device_id !== 'all') {
            $query->where('device_id', $request->device_id);
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

    public function store(Request $request): JsonResponse
    {
        $timestamp = now();
        try {
            $device = Device::where('name', $request->input('deviceName'))->first();

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found',
                ], 404);
            }

            DB::table('device_readings')->insert([
                'area_id'     => $device->area_id,
                'device_id'   => $device->id,
                'temperature' => $request->input('temperature') ?? 0,
                'humidity'    => $request->input('humidity') ?? 0,
                'battery'     => $request->input('battery') ?? 0,
                'received_at' => $timestamp,
                'created_at'  => $timestamp,
                'updated_at'  => $timestamp,
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
