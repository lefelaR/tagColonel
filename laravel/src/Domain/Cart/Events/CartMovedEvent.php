<?php

declare(strict_types=1);

namespace Domain\Cart\Events;

use Domain\Cart\ValueObjects\CartId;
use Domain\Cart\ValueObjects\Hole;
use Domain\Cart\ValueObjects\Speed;
use Domain\Course\ValueObjects\CourseId;
use Domain\Course\ValueObjects\GeoCoordinate;

// DDD: domain event published after a valid GPS fix is stored.
final readonly class CartMovedEvent
{
    public function __construct(
        public CartId $cartId,
        public CourseId $courseId,
        public GeoCoordinate $coordinate,
        public Speed $speed,
        public Hole $hole,
    ) {}
}
