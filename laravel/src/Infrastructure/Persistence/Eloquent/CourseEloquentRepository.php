<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent;

use Domain\Course\Models\Course;
use Domain\Course\Repositories\CourseRepositoryInterface;
use Domain\Course\ValueObjects\CourseId;

// Infrastructure adapter. Persistence only; course rules stay in the domain.
final class CourseEloquentRepository implements CourseRepositoryInterface
{
    public function findById(CourseId $id): ?Course
    {
        return Course::query()->find($id->value);
    }

    public function all(): array
    {
        return Course::query()->orderBy('id')->get()->all();
    }

    public function save(Course $course): void
    {
        $course->save();
    }
}
