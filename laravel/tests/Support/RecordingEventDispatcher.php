<?php

declare(strict_types=1);

namespace Tests\Support;

use Domain\Shared\Events\DomainEventDispatcher;

final class RecordingEventDispatcher implements DomainEventDispatcher
{
    /** @var list<object> */
    public array $events = [];

    public function dispatch(object $event): void
    {
        $this->events[] = $event;
    }
}
