<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceReading extends Model
{
    protected $fillable = [
        'area_id',
        'device_id',
        'battery',
        'temperature',
        'humidity',
        'received_at',
    ];

    protected $casts = [
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'battery' => 'integer',
        'received_at' => 'datetime',
    ];


    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
