<?php

declare(strict_types=1);

namespace Domain\Cart\Repositories;

use Domain\Cart\Models\Position;
use Domain\Cart\ValueObjects\CartId;
use Domain\Cart\ValueObjects\Hole;
use Domain\Cart\ValueObjects\Speed;
use Domain\Course\ValueObjects\GeoCoordinate;

// SOLID ISP: persisted GPS history. Live map reads go through CartRepositoryInterface.
interface PositionRepositoryInterface
{
    public function append(
        CartId $cartId,
        GeoCoordinate $coordinate,
        Speed $speed,
        Hole $hole,
        \DateTimeImmutable $recordedAt,
    ): void;

    public function save(Position $position): void;

    /** @return list<Position> */
    public function findLatestByCart(CartId $cartId, int $limit = 50): array;
}
