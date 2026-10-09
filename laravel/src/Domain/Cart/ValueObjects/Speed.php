<?php

declare(strict_types=1);

namespace Domain\Cart\ValueObjects;

// DDD: value object. Unit is meters per second so callers cannot pass a bare float.
final readonly class Speed
{
    private const MAX_METERS_PER_SECOND = 15.0;

    public function __construct(public float $metersPerSecond)
    {
        if (! is_finite($this->metersPerSecond)
            || $this->metersPerSecond < 0
            || $this->metersPerSecond > self::MAX_METERS_PER_SECOND
        ) {
            throw new \InvalidArgumentException(
                'Speed must be a finite value between 0 and 15 meters per second.'
            );
        }
    }
}
