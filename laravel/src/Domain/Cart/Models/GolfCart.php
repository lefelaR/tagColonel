<?php

declare(strict_types=1);

namespace Domain\Cart\Models;

use Domain\Cart\ValueObjects\CartStatus;
use Illuminate\Database\Eloquent\Model;

// DDD entity mapped by Eloquent. Fillable and casts only — no business rules.
class GolfCart extends Model
{
    protected $fillable = [
        'course_id',
        'name',
        'status',
    ];

    protected $casts = [
        'status' => CartStatus::class,
    ];
}
