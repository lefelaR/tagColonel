<?php

declare(strict_types=1);

namespace Tests\Feature;

use Domain\Cart\Repositories\CartRepositoryInterface;
use Domain\Cart\ValueObjects\CartId;
use Domain\Cart\ValueObjects\Speed;
use Domain\Course\ValueObjects\CourseId;
use Domain\Course\ValueObjects\GeoCoordinate;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

final class LiveCartRedisRepositoryTest extends TestCase
{
    private const COURSE_KEY = 'fairwayflow:course:42:live';

    private const OTHER_COURSE_KEY = 'fairwayflow:course:43:live';

    protected function tearDown(): void
    {
        $redis = Redis::connection();
        $redis->command('del', [self::COURSE_KEY]);
        $redis->command('del', [self::OTHER_COURSE_KEY]);
        $redis->command('del', ['fairwayflow:course:42:cart:7']);
        $redis->command('del', ['fairwayflow:course:42:cart:8']);
        $redis->command('del', ['fairwayflow:course:43:cart:7']);

        parent::tearDown();
    }

    public function test_geoadd_and_georadius_return_only_that_courses_carts(): void
    {
        $repository = $this->app->make(CartRepositoryInterface::class);
        $courseId = new CourseId(42);
        $otherCourseId = new CourseId(43);

        $repository->saveLivePosition(
            new CartId(7),
            $courseId,
            new GeoCoordinate(-33.9249, 18.4241),
            new Speed(3.5),
        );
        $repository->saveLivePosition(
            new CartId(8),
            $courseId,
            new GeoCoordinate(-33.9255, 18.4250),
            new Speed(1.25),
        );
        $repository->saveLivePosition(
            new CartId(7),
            $otherCourseId,
            new GeoCoordinate(-33.8000, 18.5000),
            new Speed(2),
        );

        $live = $repository->findLiveByCourse($courseId);

        $this->assertCount(2, $live);

        $byCart = [];
        foreach ($live as $cart) {
            $byCart[$cart['cart_id']] = $cart;
        }

        $this->assertEqualsWithDelta(-33.9249, $byCart[7]['latitude'], 0.0001);
        $this->assertEqualsWithDelta(18.4241, $byCart[7]['longitude'], 0.0001);
        $this->assertEqualsWithDelta(3.5, $byCart[7]['speed'], 0.001);
        $this->assertEqualsWithDelta(1.25, $byCart[8]['speed'], 0.001);
        $this->assertCount(1, $repository->findLiveByCourse($otherCourseId));
    }
}
