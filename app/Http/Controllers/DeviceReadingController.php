<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeviceReadingRequest​;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeviceReadingController extends Controller
{
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
