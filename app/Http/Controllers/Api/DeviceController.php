<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;

class DeviceController extends Controller
{
    /**
     * GET /api/devices/latest
     *
     * Mengembalikan semua device yang terdaftar beserta data reading
     * terakhirnya (battery, temperature, humidity, received_at).
     *
     * Diakses oleh Mobile App (ThermIQ) via Bearer Token (Sanctum).
     */
    public function latestReadings()
    {
        $devices = Device::with(['latestReading'])
            ->orderBy('name')
            ->get()
            ->map(function (Device $device) {
                $reading = $device->latestReading;

                return [
                    'id'         => $device->id,
                    'name'       => $device->name,
                    'is_active'  => $device->is_active,
                    'area_id'    => $device->area_id,
                    'latest_reading' => $reading ? [
                        'battery'     => $reading->battery,
                        'temperature' => $reading->temperature,
                        'humidity'    => $reading->humidity,
                        'received_at' => $reading->received_at?->toIso8601String(),
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Devices with latest readings fetched successfully.',
            'data'    => $devices,
        ]);
    }
}
