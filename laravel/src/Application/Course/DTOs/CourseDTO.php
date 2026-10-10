<?php

declare(strict_types=1);

namespace Application\Course\DTOs;

final readonly class CourseDTO
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
