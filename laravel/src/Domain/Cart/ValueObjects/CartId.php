<?php

declare(strict_types=1);

namespace Domain\Cart\ValueObjects;

// DDD: identity value object. A cart id is not interchangeable with a course id.
final readonly class CartId
{
    public function __construct(public int $value)
    {
        if ($this->value < 1) {
            throw new \InvalidArgumentException('CartId must be a positive integer.');
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
