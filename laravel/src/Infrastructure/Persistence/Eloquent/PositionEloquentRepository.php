<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent;

use Domain\Cart\Models\Position;
use Domain\Cart\Repositories\PositionRepositoryInterface;
use Domain\Cart\ValueObjects\CartId;
use Domain\Cart\ValueObjects\Hole;
use Domain\Cart\ValueObjects\Speed;
use Domain\Course\ValueObjects\GeoCoordinate;

// Infrastructure adapter for GPS history in MySQL.
final class PositionEloquentRepository implements PositionRepositoryInterface
{
    public function append(
        CartId $cartId,
        GeoCoordinate $coordinate,
        Speed $speed,
        Hole $hole,
        \DateTimeImmutable $recordedAt,
    ): void {
        $this->save(new Position([
            'cart_id' => $cartId->value,
            'latitude' => $coordinate->lat,
            'longitude' => $coordinate->lng,
            'speed' => $speed->metersPerSecond,
            'hole' => $hole->number,
            'recorded_at' => $recordedAt,
        ]));
    }

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
