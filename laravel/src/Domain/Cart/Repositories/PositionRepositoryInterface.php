<?php

declare(strict_types=1);

namespace Domain\Cart\Repositories;

use Domain\Cart\Models\Position;
use Domain\Cart\ValueObjects\CartId;

// SOLID ISP: persisted GPS history. Live map reads go through CartRepositoryInterface.
interface PositionRepositoryInterface
{
    public function save(Position $position): void;

    /** @return list<Position> */
    public function findLatestByCart(CartId $cartId, int $limit = 50): array;
}
