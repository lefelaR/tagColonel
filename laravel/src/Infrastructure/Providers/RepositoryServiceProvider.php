<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Domain\Alert\Repositories\AlertRepositoryInterface;
use Domain\Cart\Repositories\CartRepositoryInterface;
use Domain\Cart\Repositories\PositionRepositoryInterface;
use Domain\Course\Repositories\CourseRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Persistence\Eloquent\AlertEloquentRepository;
use Infrastructure\Persistence\Eloquent\CourseEloquentRepository;
use Infrastructure\Persistence\Eloquent\PositionEloquentRepository;
use Infrastructure\Persistence\Redis\LiveCartRedisRepository;

// SOLID DIP: the only place concrete repositories are chosen.
final class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CourseRepositoryInterface::class, CourseEloquentRepository::class);
        $this->app->bind(PositionRepositoryInterface::class, PositionEloquentRepository::class);
        $this->app->bind(AlertRepositoryInterface::class, AlertEloquentRepository::class);

        $this->app->bind(CartRepositoryInterface::class, function ($app): CartRepositoryInterface {
            return new LiveCartRedisRepository($app->make('redis')->connection());
        });
    }
}
