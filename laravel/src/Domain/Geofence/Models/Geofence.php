<?php

declare(strict_types=1);

namespace Domain\Geofence\Models;

use Illuminate\Database\Eloquent\Model;

// DDD entity mapped by Eloquent. Point-in-polygon lives on Polygon, not on this model.
class Geofence extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'polygon',
    ];

    protected $casts = [
        'polygon' => 'array',
    ];
}
