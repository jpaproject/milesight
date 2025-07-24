<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = [
        'name',
    ];

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}
