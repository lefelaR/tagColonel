<?php

declare(strict_types=1);

namespace Application\Alert\Listeners;

use Application\Alert\DTOs\CreateAlertDTO;
use Application\Alert\UseCases\CreateAlertUseCase;
use Domain\Round\Events\SlowPlayDetectedEvent;

// Application listener. Maps the domain event onto CreateAlertUseCase and adds no pace rules.
final class CreateAlertOnSlowPlay
{
    public function __construct(private readonly CreateAlertUseCase $createAlert) {}

    public function handle(SlowPlayDetectedEvent $event): void
    {
        $this->createAlert->execute(new CreateAlertDTO(
            courseId: $event->courseId->value,
            cartId: $event->cartId->value,
            roundId: $event->roundId->value,
            paceStatus: $event->paceStatus->value,
            message: 'Slow play detected.',
        ));
    }
}
