<?php

declare(strict_types=1);

namespace Domain\Cart\Repositories;

use Domain\Cart\ValueObjects\CartId;
use Domain\Cart\ValueObjects\Speed;
use Domain\Course\ValueObjects\CourseId;
use Domain\Course\ValueObjects\GeoCoordinate;

// SOLID ISP: live positions only. Position history is PositionRepositoryInterface.
interface CartRepositoryInterface
{
    public function saveLivePosition(
        CartId $cartId,
        CourseId $courseId,
        GeoCoordinate $coordinate,
        Speed $speed,
    ): void;

    /**
     * @return list<array{cart_id: int, latitude: float, longitude: float, speed: float}>
     */
    public function findLiveByCourse(CourseId $courseId): array;
}
