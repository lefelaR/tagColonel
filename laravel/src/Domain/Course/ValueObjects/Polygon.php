<?php

declare(strict_types=1);

namespace Domain\Course\ValueObjects;

// DDD: value object. Ray casting is pure geometry and has no framework dependency.
final readonly class Polygon
{
    /** @var list<GeoCoordinate> */
    public array $vertices;

    /** @param list<GeoCoordinate> $vertices */
    public function __construct(array $vertices)
    {
        if (count($vertices) < 3) {
            throw new \InvalidArgumentException('A polygon requires at least 3 vertices.');
        }

        $copied = [];

        foreach ($vertices as $vertex) {
            if (! $vertex instanceof GeoCoordinate) {
                throw new \InvalidArgumentException('Every polygon vertex must be a GeoCoordinate.');
            }

            $copied[] = $vertex;
        }

        $this->vertices = $copied;
    }

    public function contains(GeoCoordinate $point): bool
    {
        $inside = false;
        $count = count($this->vertices);

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $yi = $this->vertices[$i]->lat;
            $xi = $this->vertices[$i]->lng;
            $yj = $this->vertices[$j]->lat;
            $xj = $this->vertices[$j]->lng;

            $crossesLatitude = ($yi > $point->lat) !== ($yj > $point->lat);

            if (! $crossesLatitude) {
                continue;
            }

            $intersectionLng = (($xj - $xi) * ($point->lat - $yi) / ($yj - $yi)) + $xi;

            if ($point->lng < $intersectionLng) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }
}
