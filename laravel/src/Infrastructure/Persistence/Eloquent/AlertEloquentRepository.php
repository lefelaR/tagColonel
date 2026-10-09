<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent;

use Domain\Alert\Models\PaceAlert;
use Domain\Alert\Repositories\AlertRepositoryInterface;
use Domain\Course\ValueObjects\CourseId;

// Infrastructure adapter. Open alerts are a query, not a rule on the PaceAlert model.
final class AlertEloquentRepository implements AlertRepositoryInterface
{
    public function save(PaceAlert $alert): void
    {
        $alert->save();
    }

    public function findUnresolvedByCourse(CourseId $courseId): array
    {
        return PaceAlert::query()
            ->where('course_id', $courseId->value)
            ->whereNull('resolved_at')
            ->orderByDesc('id')
            ->get()
            ->all();
    }
}
