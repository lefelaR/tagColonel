<?php

declare(strict_types=1);

namespace Domain\Course\Repositories;

use Domain\Course\Models\Course;
use Domain\Course\ValueObjects\CourseId;

// SOLID DIP: use cases depend on this interface, not on Course::query().
interface CourseRepositoryInterface
{
    public function findById(CourseId $id): ?Course;

    /** @return list<Course> */
    public function all(): array;

    public function save(Course $course): void;
}
