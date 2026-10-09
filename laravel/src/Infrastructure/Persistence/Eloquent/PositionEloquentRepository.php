<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent;

use Domain\Cart\Models\Position;
use Domain\Cart\Repositories\PositionRepositoryInterface;
use Domain\Cart\ValueObjects\CartId;

// Infrastructure adapter for GPS history in MySQL.
final class PositionEloquentRepository implements PositionRepositoryInterface
{
    public function save(Position $position): void
    {
        $position->save();
    }

    public function findLatestByCart(CartId $cartId, int $limit = 50): array
    {
        if ($limit < 1) {
            throw new \InvalidArgumentException('Position history limit must be at least 1.');
        }

        return Position::query()
            ->where('cart_id', $cartId->value)
            ->orderByDesc('recorded_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->all();
    }
}
