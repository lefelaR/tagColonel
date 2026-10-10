<?php

declare(strict_types=1);

namespace Domain\Round\ValueObjects;

// DDD: identity value object. A round id is not interchangeable with a cart or course id.
final readonly class RoundId
{
    public function __construct(public int $value)
    {
        if ($this->value < 1) {
            throw new \InvalidArgumentException('RoundId must be a positive integer.');
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
