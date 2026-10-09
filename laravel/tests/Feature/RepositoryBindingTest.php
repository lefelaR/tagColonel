<?php

declare(strict_types=1);

namespace Tests\Feature;

use Domain\Alert\Repositories\AlertRepositoryInterface;
use Domain\Cart\Repositories\CartRepositoryInterface;
use Domain\Cart\Repositories\PositionRepositoryInterface;
use Domain\Course\Repositories\CourseRepositoryInterface;
use Infrastructure\Persistence\Eloquent\AlertEloquentRepository;
use Infrastructure\Persistence\Eloquent\CourseEloquentRepository;
use Infrastructure\Persistence\Eloquent\PositionEloquentRepository;
use Infrastructure\Persistence\Redis\LiveCartRedisRepository;
use Tests\TestCase;

final class RepositoryBindingTest extends TestCase
{
    public function test_interfaces_resolve_to_their_adapters(): void
    {
        $this->assertInstanceOf(CourseEloquentRepository::class, $this->app->make(CourseRepositoryInterface::class));
        $this->assertInstanceOf(PositionEloquentRepository::class, $this->app->make(PositionRepositoryInterface::class));
        $this->assertInstanceOf(AlertEloquentRepository::class, $this->app->make(AlertRepositoryInterface::class));
        $this->assertInstanceOf(LiveCartRedisRepository::class, $this->app->make(CartRepositoryInterface::class));
    }
}
