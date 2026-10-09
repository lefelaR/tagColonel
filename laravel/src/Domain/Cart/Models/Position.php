<?php

declare(strict_types=1);

namespace Domain\Cart\Models;

use Illuminate\Database\Eloquent\Model;

// DDD entity mapped by Eloquent. Coordinates are stored here; GeoCoordinate validates them.
class Position extends Model
{
    protected $fillable = [
        'cart_id',
        'round_id',
        'latitude',
        'longitude',
        'speed',
        'hole',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'speed' => 'float',
        'hole' => 'integer',
        'recorded_at' => 'datetime',
    ];
}
