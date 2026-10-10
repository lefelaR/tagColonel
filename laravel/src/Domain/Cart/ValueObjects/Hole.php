<?php

declare(strict_types=1);

namespace Domain\Cart\ValueObjects;

// DDD: value object. A hole number is not a raw int at the domain boundary.
final readonly class Hole
{
    public function __construct(public int $number)
    {
        if ($this->number < 1 || $this->number > 18) {
            throw new \InvalidArgumentException('Hole must be between 1 and 18.');
        }
    }
}
