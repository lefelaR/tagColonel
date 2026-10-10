<?php

declare(strict_types=1);

namespace Domain\Geofence\Services;

use Domain\Course\ValueObjects\GeoCoordinate;
use Domain\Course\ValueObjects\Polygon;

// DDD: domain service. Pure point-in-polygon check, no framework dependency.
final class GeofenceService
{
    public function isInside(GeoCoordinate $point, Polygon $polygon): bool
    {
        return $polygon->contains($point);
    }
}
