<?php

declare(strict_types=1);

namespace Tests\Support;

use Domain\Alert\Models\PaceAlert;
use Domain\Alert\Repositories\AlertRepositoryInterface;
use Domain\Course\ValueObjects\CourseId;

final class InMemoryAlertRepository implements AlertRepositoryInterface
{
    /** @var array<int, PaceAlert> */
    private array $alerts = [];

    private int $nextId = 1;

    public function save(PaceAlert $alert): void
    {
        if ($alert->id === null) {
            $alert->id = $this->nextId++;
        }

        $this->alerts[(int) $alert->id] = $alert;
    }

    public function findUnresolvedByCourse(CourseId $courseId): array
    {
        $matched = [];

        foreach ($this->alerts as $alert) {
            if ((int) $alert->course_id === $courseId->value && $alert->resolved_at === null) {
                $matched[] = $alert;
            }
        }

        usort($matched, static fn (PaceAlert $left, PaceAlert $right): int => (int) $right->id <=> (int) $left->id);

        return $matched;
    }
}
