<?php

declare(strict_types=1);

namespace Domain\Round\Repositories;

use Domain\Round\Models\Round;
use Domain\Round\ValueObjects\RoundId;

// SOLID DIP: CalculatePaceUseCase depends on this interface, not on Round::query().
interface RoundRepositoryInterface
{
    public function findById(RoundId $id): ?Round;

    public function save(Round $round): void;
}
