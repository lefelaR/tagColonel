<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use Domain\Course\ValueObjects\GeoCoordinate;
use Domain\Course\ValueObjects\Polygon;
use Domain\Geofence\Services\GeofenceService;
use Domain\Round\Services\PaceCalculationService;
use Domain\Round\ValueObjects\ActualTime;
use Domain\Round\ValueObjects\PaceStatus;
use Domain\Round\ValueObjects\ParTime;
use PHPUnit\Framework\TestCase;

final class PaceAndGeofenceServiceTest extends TestCase
{
    private PaceCalculationService $pace;

    private GeofenceService $geofence;

    protected function setUp(): void
    {
        $this->pace = new PaceCalculationService();
        $this->geofence = new GeofenceService();
    }

    public function test_pace_is_on_pace_at_or_under_par(): void
    {
        $par = new ParTime(100);

        $this->assertSame(PaceStatus::OnPace, $this->pace->calculate(new ActualTime(100), $par));
        $this->assertSame(PaceStatus::OnPace, $this->pace->calculate(new ActualTime(90), $par));
    }

    public function test_pace_is_slow_until_fifteen_percent_over_par(): void
    {
        $status = $this->pace->calculate(new ActualTime(115), new ParTime(100));

        $this->assertSame(PaceStatus::Slow, $status);
    }

    public function test_pace_is_very_slow_past_fifteen_percent(): void
    {
        $status = $this->pace->calculate(new ActualTime(116), new ParTime(100));

        $this->assertSame(PaceStatus::VerySlow, $status);
    }

    public function test_geofence_service_uses_the_polygon(): void
    {
        $square = new Polygon([
            new GeoCoordinate(0, 0),
            new GeoCoordinate(0, 2),
            new GeoCoordinate(2, 2),
            new GeoCoordinate(2, 0),
        ]);

        $this->assertTrue($this->geofence->isInside(new GeoCoordinate(1, 1), $square));
        $this->assertFalse($this->geofence->isInside(new GeoCoordinate(3, 1), $square));
    }
}
