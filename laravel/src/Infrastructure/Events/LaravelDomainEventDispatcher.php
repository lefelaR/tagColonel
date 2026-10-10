<?php

declare(strict_types=1);

namespace Infrastructure\Events;

use Domain\Shared\Events\DomainEventDispatcher;
use Illuminate\Contracts\Events\Dispatcher;

// Infrastructure adapter. The framework bus stays behind DomainEventDispatcher.
final class LaravelDomainEventDispatcher implements DomainEventDispatcher
{
    public function __construct(private readonly Dispatcher $dispatcher) {}

    public function dispatch(object $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}
