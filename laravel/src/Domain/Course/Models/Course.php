<?php

declare(strict_types=1);

namespace Domain\Course\Models;

use Illuminate\Database\Eloquent\Model;

// DDD entity mapped by Eloquent. Fillable and casts only — no business rules.
class Course extends Model
{
    protected $fillable = [
        'name',
    ];
}
