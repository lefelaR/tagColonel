<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Application\Alert\Listeners\CreateAlertOnSlowPlay;
use Application\Shared\TransactionRunner;
use Domain\Round\Events\SlowPlayDetectedEvent;
use Domain\Round\Services\PaceCalculationService;
use Domain\Round\Services\PaceCalculator;
use Domain\Shared\Events\DomainEventDispatcher;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Events\LaravelDomainEventDispatcher;
use Infrastructure\Persistence\Transaction\LaravelTransactionRunner;

// SOLID DIP: bind domain ports, and send SlowPlayDetectedEvent to the application listener.
final class DomainBridgeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaceCalculator::class, PaceCalculationService::class);
        $this->app->bind(DomainEventDispatcher::class, LaravelDomainEventDispatcher::class);
        $this->app->bind(TransactionRunner::class, LaravelTransactionRunner::class);
    }

    public function boot(Dispatcher $events): void
    {
        $events->listen(SlowPlayDetectedEvent::class, [CreateAlertOnSlowPlay::class, 'handle']);
    }
}
