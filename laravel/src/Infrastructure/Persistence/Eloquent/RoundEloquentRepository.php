<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent;

use Domain\Round\Models\Round;
use Domain\Round\Repositories\RoundRepositoryInterface;
use Domain\Round\ValueObjects\RoundId;

// Infrastructure adapter. Pace math stays in PaceCalculationService.
final class RoundEloquentRepository implements RoundRepositoryInterface
{
    public function findById(RoundId $id): ?Round
    {
        return Round::query()->find($id->value);
    }

    public function save(Round $round): void
    {
        $round->save();
    }
}
