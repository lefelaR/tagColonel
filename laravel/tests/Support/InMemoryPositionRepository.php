<?php

declare(strict_types=1);

namespace Tests\Support;

use Domain\Cart\Models\Position;
use Domain\Cart\Repositories\PositionRepositoryInterface;
use Domain\Cart\ValueObjects\CartId;
use Domain\Cart\ValueObjects\Hole;
use Domain\Cart\ValueObjects\Speed;
use Domain\Course\ValueObjects\GeoCoordinate;

final class InMemoryPositionRepository implements PositionRepositoryInterface
{
    /** @var list<Position> */
    private array $positions = [];

    /** @var list<array{cart_id: int, latitude: float, longitude: float, speed: float, hole: int}> */
    public array $appended = [];

    public function append(
        CartId $cartId,
        GeoCoordinate $coordinate,
        Speed $speed,
        Hole $hole,
        \DateTimeImmutable $recordedAt,
    ): void {
        $this->appended[] = [
            'cart_id' => $cartId->value,
            'latitude' => $coordinate->lat,
            'longitude' => $coordinate->lng,
            'speed' => $speed->metersPerSecond,
            'hole' => $hole->number,
        ];
    }

    public function save(Position $position): void
    {
        $this->positions[] = $position;
    }

    public function findLatestByCart(CartId $cartId, int $limit = 50): array
    {
        if ($limit < 1) {
            throw new \InvalidArgumentException('Position history limit must be at least 1.');
        }

        $matched = [];

        foreach ($this->positions as $position) {
            if ((int) $position->cart_id === $cartId->value) {
                $matched[] = $position;
            }
        }

        return array_slice(array_reverse($matched), 0, $limit);
    }
}
