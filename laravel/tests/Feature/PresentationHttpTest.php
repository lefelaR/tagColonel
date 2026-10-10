<?php

declare(strict_types=1);

namespace Tests\Feature;

use Application\Shared\TransactionRunner;
use Domain\Alert\Repositories\AlertRepositoryInterface;
use Domain\Cart\Repositories\CartRepositoryInterface;
use Domain\Cart\Repositories\PositionRepositoryInterface;
use Domain\Course\Models\Course;
use Domain\Course\Repositories\CourseRepositoryInterface;
use Tests\Support\ImmediateTransactionRunner;
use Tests\Support\InMemoryAlertRepository;
use Tests\Support\InMemoryCartRepository;
use Tests\Support\InMemoryCourseRepository;
use Tests\Support\InMemoryPositionRepository;
use Tests\TestCase;

final class PresentationHttpTest extends TestCase
{
    private InMemoryCourseRepository $courses;

    protected function setUp(): void
    {
        parent::setUp();

        $this->courses = new InMemoryCourseRepository();
        $this->app->instance(CourseRepositoryInterface::class, $this->courses);
        $this->app->instance(CartRepositoryInterface::class, new InMemoryCartRepository());
        $this->app->instance(PositionRepositoryInterface::class, new InMemoryPositionRepository());
        $this->app->instance(AlertRepositoryInterface::class, new InMemoryAlertRepository());
        $this->app->instance(TransactionRunner::class, new ImmediateTransactionRunner());
    }

    public function test_courses_live_carts_ingest_and_alerts_stay_behind_use_cases(): void
    {
        $course = new Course(['name' => 'Peninsula']);
        $course->id = 1;
        $this->courses->save($course);

        $this->getJson('/api/courses')
            ->assertOk()
            ->assertJsonPath('data.0.id', 1)
            ->assertJsonPath('data.0.name', 'Peninsula');

        $this->postJson('/api/ingest', [
            'cart_id' => 7,
            'course_id' => 1,
            'lat' => -33.9249,
            'lng' => 18.4241,
            'speed' => 3.5,
            'hole' => 4,
        ])->assertCreated()
            ->assertJsonPath('data.cart_id', 7)
            ->assertJsonPath('data.course_id', 1);

        $this->getJson('/api/courses/1/carts/live')
            ->assertOk()
            ->assertJsonPath('data.0.cart_id', 7)
            ->assertJsonPath('data.0.speed', 3.5);

        $this->postJson('/api/alerts', [
            'course_id' => 1,
            'cart_id' => 7,
            'pace_status' => 'slow',
            'message' => 'Behind the group.',
        ])->assertCreated()
            ->assertJsonPath('data.pace_status', 'slow')
            ->assertJsonPath('data.cart_id', 7);

        $this->getJson('/api/alerts?course_id=1')
            ->assertOk()
            ->assertJsonPath('data.0.message', 'Behind the group.');
    }

    public function test_ingest_rejects_a_latitude_outside_the_value_object_range(): void
    {
        $this->postJson('/api/ingest', [
            'cart_id' => 7,
            'course_id' => 1,
            'lat' => 91,
            'lng' => 18.4,
            'speed' => 1,
            'hole' => 1,
        ])->assertStatus(422)->assertJsonValidationErrors(['lat']);
    }

    public function test_alert_index_requires_a_course_id(): void
    {
        $this->getJson('/api/alerts')->assertStatus(422)->assertJsonValidationErrors(['course_id']);
    }
}
