<?php

declare(strict_types=1);

namespace Application\Course\UseCases;

use Application\Course\DTOs\CourseDTO;
use Application\Course\DTOs\CourseListResultDTO;
use Application\Course\DTOs\ListCoursesDTO;
use Domain\Course\Models\Course;
use Domain\Course\Repositories\CourseRepositoryInterface;

// SOLID DIP: reads through CourseRepositoryInterface. No Course::query() here.
final class ListCoursesUseCase
{
    public function __construct(private readonly CourseRepositoryInterface $courses) {}

    public function execute(ListCoursesDTO $dto): CourseListResultDTO
    {
        $courses = [];

        foreach ($this->courses->all() as $course) {
            $courses[] = $this->toDto($course);
        }

        return new CourseListResultDTO($courses);
    }

    private function toDto(Course $course): CourseDTO
    {
        return new CourseDTO(
            (int) $course->id,
            (string) $course->name,
            (float) ($course->latitude ?? 0),
            (float) ($course->longitude ?? 0),
        );
    }
}
