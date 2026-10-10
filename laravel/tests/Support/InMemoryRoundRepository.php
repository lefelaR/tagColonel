<?php

declare(strict_types=1);

namespace Tests\Support;

use Domain\Round\Models\Round;
use Domain\Round\Repositories\RoundRepositoryInterface;
use Domain\Round\ValueObjects\RoundId;

final class InMemoryRoundRepository implements RoundRepositoryInterface
{
    /** @var array<int, Round> */
    private array $rounds = [];

    public function findById(RoundId $id): ?Round
    {
        return $this->rounds[$id->value] ?? null;
    }

    public function save(Round $round): void
    {
        $this->rounds[(int) $round->id] = $round;
    }
}
