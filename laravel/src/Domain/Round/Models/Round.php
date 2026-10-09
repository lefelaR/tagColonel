<?php

declare(strict_types=1);

namespace Domain\Round\Models;

use Domain\Round\ValueObjects\PaceStatus;
use Illuminate\Database\Eloquent\Model;

// DDD entity mapped by Eloquent. Pace rules live in a domain service, not on this model.
class Round extends Model
{
    protected $fillable = [
        'course_id',
        'cart_id',
        'started_at',
        'ended_at',
        'pace_status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'pace_status' => PaceStatus::class,
    ];
}
