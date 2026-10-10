<?php

declare(strict_types=1);

namespace Domain\Shared\Events;

// SOLID DIP: application code dispatches domain events without a framework bus.
interface DomainEventDispatcher
{
    public function dispatch(object $event): void;
}
