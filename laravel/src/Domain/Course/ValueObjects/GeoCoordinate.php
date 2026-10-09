<?php

declare(strict_types=1);

namespace Domain\Course\ValueObjects;

// DDD: value object. Invalid coordinates are rejected here, not in controllers or models.
final readonly class GeoCoordinate
{
    public function __construct(public float $lat, public float $lng)
    {
        if (! is_finite($this->lat) || ! is_finite($this->lng)) {
            throw new \InvalidArgumentException('Latitude and longitude must be finite numbers.');
        }

        if ($this->lat < -90 || $this->lat > 90) {
            throw new \InvalidArgumentException('Latitude must be between -90 and 90.');
        }

        if ($this->lng < -180 || $this->lng > 180) {
            throw new \InvalidArgumentException('Longitude must be between -180 and 180.');
        }
    }

    public function equals(self $other): bool
    {
        return $this->lat === $other->lat && $this->lng === $other->lng;
    }
}
