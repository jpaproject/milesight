<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = [
        'terminal_id',
        'name',
    ];

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function deviceReadings(): HasMany
    {
        return $this->hasMany(DeviceReading::class);
    }
}
