<?php

declare(strict_types=1);

namespace Tests\Support;

use Domain\Cart\Repositories\CartRepositoryInterface;
use Domain\Cart\ValueObjects\CartId;
use Domain\Cart\ValueObjects\Speed;
use Domain\Course\ValueObjects\CourseId;
use Domain\Course\ValueObjects\GeoCoordinate;

final class InMemoryCartRepository implements CartRepositoryInterface
{
    /** @var array<int, array<int, array{cart_id: int, latitude: float, longitude: float, speed: float}>> */
    private array $byCourse = [];

    public function saveLivePosition(
        CartId $cartId,
        CourseId $courseId,
        GeoCoordinate $coordinate,
        Speed $speed,
    ): void {
        $this->byCourse[$courseId->value][$cartId->value] = [
            'cart_id' => $cartId->value,
            'latitude' => $coordinate->lat,
            'longitude' => $coordinate->lng,
            'speed' => $speed->metersPerSecond,
        ];
    }

    public function findLiveByCourse(CourseId $courseId): array
    {
        return array_values($this->byCourse[$courseId->value] ?? []);
    }
}
