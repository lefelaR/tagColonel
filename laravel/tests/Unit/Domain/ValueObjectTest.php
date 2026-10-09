<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use Domain\Cart\ValueObjects\CartId;
use Domain\Cart\ValueObjects\Speed;
use Domain\Course\ValueObjects\CourseId;
use Domain\Course\ValueObjects\GeoCoordinate;
use Domain\Course\ValueObjects\Polygon;
use PHPUnit\Framework\TestCase;

final class ValueObjectTest extends TestCase
{
    public function test_geo_coordinate_accepts_a_cape_town_point(): void
    {
        $point = new GeoCoordinate(-33.9249, 18.4241);

        $this->assertSame(-33.9249, $point->lat);
        $this->assertSame(18.4241, $point->lng);
    }

    public function test_geo_coordinate_rejects_impossible_values(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new GeoCoordinate(91, 0);
    }

    public function test_geo_coordinate_rejects_non_finite_values(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new GeoCoordinate(NAN, 0);
    }

    public function test_polygon_ray_cast_detects_inside_and_outside(): void
    {
        $square = new Polygon([
            new GeoCoordinate(0, 0),
            new GeoCoordinate(0, 2),
            new GeoCoordinate(2, 2),
            new GeoCoordinate(2, 0),
        ]);

        $this->assertTrue($square->contains(new GeoCoordinate(1, 1)));
        $this->assertFalse($square->contains(new GeoCoordinate(3, 1)));
        $this->assertFalse($square->contains(new GeoCoordinate(-1, 1)));
    }

    public function test_polygon_requires_three_vertices(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Polygon([
            new GeoCoordinate(0, 0),
            new GeoCoordinate(1, 1),
        ]);
    }

    public function test_course_and_cart_ids_must_be_positive(): void
    {
        $courseId = new CourseId(4);
        $cartId = new CartId(30);

        $this->assertTrue($courseId->equals(new CourseId(4)));
        $this->assertFalse($courseId->equals(new CourseId(5)));
        $this->assertSame('30', (string) $cartId);

        try {
            new CourseId(0);
            $this->fail('CourseId accepted 0.');
        } catch (\InvalidArgumentException) {
        }

        $this->expectException(\InvalidArgumentException::class);
        new CartId(-1);
    }

    public function test_speed_rejects_negative_and_absurd_values(): void
    {
        $stopped = new Speed(0);
        $rolling = new Speed(4.2);

        $this->assertSame(0.0, $stopped->metersPerSecond);
        $this->assertSame(4.2, $rolling->metersPerSecond);

        $this->expectException(\InvalidArgumentException::class);
        new Speed(-0.1);
    }
}
