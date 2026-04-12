<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    protected $fillable = [
        'area_id',
        'name',
        'is_active',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function deviceReadings()
    {
        return $this->hasMany(DeviceReading::class);
    }

    /**
     * Relasi ke reading terakhir berdasarkan received_at.
     * Digunakan oleh API mobile untuk mengambil snapshot terkini setiap device.
     */
    public function latestReading()
    {
        return $this->hasOne(DeviceReading::class)
            ->latestOfMany('received_at');
    }
}
