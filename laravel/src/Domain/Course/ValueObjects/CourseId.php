<?php

declare(strict_types=1);

namespace Domain\Course\ValueObjects;

// DDD: identity value object. Callers cannot pass a raw int where a course id is required.
final readonly class CourseId
{
    public function __construct(public int $value)
    {
        if ($this->value < 1) {
            throw new \InvalidArgumentException('CourseId must be a positive integer.');
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
