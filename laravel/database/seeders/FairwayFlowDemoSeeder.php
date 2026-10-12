<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Cart\Models\GolfCart;
use Domain\Cart\ValueObjects\CartStatus;
use Domain\Course\Models\Course;
use Domain\Geofence\Models\Geofence;
use Illuminate\Database\Seeder;

// Demo fleet: Rondebosch plus three Cape Town courses, each with thirty carts and two zones.
class FairwayFlowDemoSeeder extends Seeder
{
    private const CARTS_PER_COURSE = 30;

    public function run(): void
    {
        foreach ($this->courses() as $index => $course) {
            $saved = Course::query()->updateOrCreate(
                ['id' => $index + 1],
                [
                    'name' => $course['name'],
                    'latitude' => $course['latitude'],
                    'longitude' => $course['longitude'],
                ],
            );

            $this->seedCarts($saved, $index);
            $this->seedZones($saved, $course['latitude'], $course['longitude']);
        }
    }

    /** @param array{name: string, latitude: float, longitude: float} $course */
    private function seedCarts(Course $course, int $index): void
    {
        $firstCartId = ($index * self::CARTS_PER_COURSE) + 1;

        for ($offset = 0; $offset < self::CARTS_PER_COURSE; $offset++) {
            GolfCart::query()->updateOrCreate(
                ['id' => $firstCartId + $offset],
                [
                    'course_id' => $course->id,
                    'name' => 'Cart '.($offset + 1),
                    'status' => CartStatus::InRound,
                ],
            );
        }
    }

    private function seedZones(Course $course, float $latitude, float $longitude): void
    {
        $zones = [
            'Clubhouse' => $this->square($latitude + 0.0016, $longitude, 0.0010, 0.0022),
            'Maintenance yard' => $this->square($latitude - 0.0012, $longitude, 0.0010, 0.0025),
        ];

        foreach ($zones as $name => $polygon) {
            Geofence::query()->updateOrCreate(
                ['course_id' => $course->id, 'name' => $name],
                ['polygon' => $polygon],
            );
        }
    }

    /** @return list<array{lat: float, lng: float}> */
    private function square(float $latitude, float $longitude, float $halfLat, float $halfLng): array
    {
        return [
            ['lat' => $latitude + $halfLat, 'lng' => $longitude - $halfLng],
            ['lat' => $latitude + $halfLat, 'lng' => $longitude + $halfLng],
            ['lat' => $latitude - $halfLat, 'lng' => $longitude + $halfLng],
            ['lat' => $latitude - $halfLat, 'lng' => $longitude - $halfLng],
        ];
    }

    /** @return list<array{name: string, latitude: float, longitude: float}> */
    private function courses(): array
    {
        return [
            ['name' => 'Rondebosch Golf Club', 'latitude' => -33.9560, 'longitude' => 18.4943],
            ['name' => 'Royal Cape Golf Club', 'latitude' => -34.0136, 'longitude' => 18.4851],
            ['name' => 'Milnerton Golf Club', 'latitude' => -33.8617, 'longitude' => 18.4927],
            ['name' => 'Steenberg Golf Club', 'latitude' => -34.0722, 'longitude' => 18.4279],
        ];
    }
}
