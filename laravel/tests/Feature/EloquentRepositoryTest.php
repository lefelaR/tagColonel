<?php

declare(strict_types=1);

namespace Tests\Feature;

use Domain\Alert\Models\PaceAlert;
use Domain\Alert\Repositories\AlertRepositoryInterface;
use Domain\Cart\Models\GolfCart;
use Domain\Cart\Models\Position;
use Domain\Cart\Repositories\PositionRepositoryInterface;
use Domain\Cart\ValueObjects\CartId;
use Domain\Cart\ValueObjects\CartStatus;
use Domain\Course\Models\Course;
use Domain\Course\Repositories\CourseRepositoryInterface;
use Domain\Course\ValueObjects\CourseId;
use Domain\Round\ValueObjects\PaceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class EloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_position_and_alert_repositories_round_trip(): void
    {
        $courses = $this->app->make(CourseRepositoryInterface::class);
        $positions = $this->app->make(PositionRepositoryInterface::class);
        $alerts = $this->app->make(AlertRepositoryInterface::class);

        $course = new Course(['name' => 'Westlake']);
        $courses->save($course);

        $found = $courses->findById(new CourseId($course->id));
        $this->assertNotNull($found);
        $this->assertSame('Westlake', $found->name);
        $this->assertCount(1, $courses->all());

        $cart = new GolfCart([
            'course_id' => $course->id,
            'name' => 'Cart 1',
            'status' => CartStatus::Available,
        ]);
        $cart->save();
        $cart->refresh();
        $this->assertSame(CartStatus::Available, $cart->status);

        $position = new Position([
            'cart_id' => $cart->id,
            'latitude' => -33.9249,
            'longitude' => 18.4241,
            'speed' => 2.5,
            'hole' => 1,
            'recorded_at' => now(),
        ]);
        $positions->save($position);

        $history = $positions->findLatestByCart(new CartId($cart->id));
        $this->assertCount(1, $history);
        $this->assertEqualsWithDelta(-33.9249, $history[0]->latitude, 0.0000001);
        $this->assertEqualsWithDelta(18.4241, $history[0]->longitude, 0.0000001);

        $open = new PaceAlert([
            'course_id' => $course->id,
            'cart_id' => $cart->id,
            'pace_status' => PaceStatus::Slow,
            'message' => 'Behind the group ahead',
        ]);
        $alerts->save($open);

        $resolved = new PaceAlert([
            'course_id' => $course->id,
            'cart_id' => $cart->id,
            'pace_status' => PaceStatus::OnPace,
            'resolved_at' => now(),
        ]);
        $alerts->save($resolved);

        $unresolved = $alerts->findUnresolvedByCourse(new CourseId($course->id));
        $this->assertCount(1, $unresolved);
        $this->assertSame(PaceStatus::Slow, $unresolved[0]->pace_status);
    }
}
