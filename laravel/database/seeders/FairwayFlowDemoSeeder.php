<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Cart\Models\GolfCart;
use Domain\Cart\ValueObjects\CartStatus;
use Domain\Course\Models\Course;
use Domain\Geofence\Models\Geofence;
use Illuminate\Database\Seeder;

// Demo fleet: one course, thirty carts, two restricted zones near the GPS simulator.
class FairwayFlowDemoSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::query()->updateOrCreate(
            ['id' => 1],
            ['name' => 'Peninsula'],
        );

        for ($number = 1; $number <= 30; $number++) {
            GolfCart::query()->updateOrCreate(
                ['id' => $number],
                [
                    'course_id' => $course->id,
                    'name' => 'Cart '.$number,
                    'status' => CartStatus::InRound,
                ],
            );
        }

        foreach ($this->zones() as $name => $polygon) {
            Geofence::query()->updateOrCreate(
                ['course_id' => $course->id, 'name' => $name],
                ['polygon' => $polygon],
            );
        }
    }

    /** @return array<string, list<array{lat: float, lng: float}>> */
    private function zones(): array
    {
        return [
            'Clubhouse' => [
                ['lat' => -33.9235, 'lng' => 18.4230],
                ['lat' => -33.9235, 'lng' => 18.4250],
                ['lat' => -33.9225, 'lng' => 18.4250],
                ['lat' => -33.9225, 'lng' => 18.4230],
            ],
            'Maintenance yard' => [
                ['lat' => -33.9265, 'lng' => 18.4230],
                ['lat' => -33.9265, 'lng' => 18.4255],
                ['lat' => -33.9255, 'lng' => 18.4255],
                ['lat' => -33.9255, 'lng' => 18.4230],
            ],
        ];
    }
}
