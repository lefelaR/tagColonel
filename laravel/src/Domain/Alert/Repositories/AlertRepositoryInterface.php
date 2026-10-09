<?php

declare(strict_types=1);

namespace Domain\Alert\Repositories;

use Domain\Alert\Models\PaceAlert;
use Domain\Course\ValueObjects\CourseId;

// SOLID DIP: alert persistence is behind this interface, not PaceAlert::query().
interface AlertRepositoryInterface
{
    public function save(PaceAlert $alert): void;

    /** @return list<PaceAlert> */
    public function findUnresolvedByCourse(CourseId $courseId): array;
}
