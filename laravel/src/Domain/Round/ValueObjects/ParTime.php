<?php

declare(strict_types=1);

namespace Domain\Round\ValueObjects;

// DDD: value object. Expected round time in seconds. Zero par cannot define a pace ratio.
final readonly class ParTime
{
    public function __construct(public int $seconds)
    {
        if ($this->seconds < 1) {
            throw new \InvalidArgumentException('ParTime must be at least 1 second.');
        }
    }
}
