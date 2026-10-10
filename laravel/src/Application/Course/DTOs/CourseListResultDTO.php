<?php

declare(strict_types=1);

namespace Application\Course\DTOs;

final readonly class CourseListResultDTO
{
    /** @param list<CourseDTO> $courses */
    public function __construct(public array $courses) {}
}
