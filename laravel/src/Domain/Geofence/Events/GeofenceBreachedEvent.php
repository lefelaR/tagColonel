<?php

declare(strict_types=1);

namespace Domain\Geofence\Events;

use Domain\Cart\ValueObjects\CartId;
use Domain\Course\ValueObjects\CourseId;
use Domain\Course\ValueObjects\GeoCoordinate;

// DDD: domain event. A breach is a cart position inside a restricted polygon.
final readonly class GeofenceBreachedEvent
{
    public function __construct(
        public CourseId $courseId,
        public CartId $cartId,
        public GeoCoordinate $position,
    ) {}
}
