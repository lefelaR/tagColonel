<?php

declare(strict_types=1);

namespace Tests\Support;

use Domain\Course\Models\Course;
use Domain\Course\Repositories\CourseRepositoryInterface;
use Domain\Course\ValueObjects\CourseId;

final class InMemoryCourseRepository implements CourseRepositoryInterface
{
    /** @var array<int, Course> */
    private array $courses = [];

    private int $nextId = 1;

    public function findById(CourseId $id): ?Course
    {
        return $this->courses[$id->value] ?? null;
    }

    public function all(): array
    {
        $courses = array_values($this->courses);

        usort($courses, static fn (Course $left, Course $right): int => (int) $left->id <=> (int) $right->id);

        return $courses;
    }

    public function save(Course $course): void
    {
        if ($course->id === null) {
            $course->id = $this->nextId++;
        }

        $this->courses[(int) $course->id] = $course;
        $this->nextId = max($this->nextId, (int) $course->id + 1);
    }
}
