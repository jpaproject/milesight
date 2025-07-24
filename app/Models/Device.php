<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    protected $fillable = [
        'area_id',
        'name',
        'topic',
        'is_active',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}
