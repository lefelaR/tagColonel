<?php

declare(strict_types=1);

namespace Domain\Round\ValueObjects;

// DDD: value object. Elapsed round time in seconds, distinct from par time.
final readonly class ActualTime
{
    public function __construct(public int $seconds)
    {
        if ($this->seconds < 0) {
            throw new \InvalidArgumentException('ActualTime cannot be negative.');
        }
    }
}
