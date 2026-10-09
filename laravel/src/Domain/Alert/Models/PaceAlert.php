<?php

declare(strict_types=1);

namespace Domain\Alert\Models;

use Domain\Round\ValueObjects\PaceStatus;
use Illuminate\Database\Eloquent\Model;

// DDD entity mapped by Eloquent. Creating or resolving an alert is a use case, not a model method.
class PaceAlert extends Model
{
    protected $fillable = [
        'course_id',
        'cart_id',
        'round_id',
        'pace_status',
        'message',
        'resolved_at',
    ];

    protected $casts = [
        'pace_status' => PaceStatus::class,
        'resolved_at' => 'datetime',
    ];
}
