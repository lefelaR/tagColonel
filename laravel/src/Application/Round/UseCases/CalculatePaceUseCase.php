<?php

declare(strict_types=1);

namespace Application\Round\UseCases;

use Application\Round\DTOs\CalculatePaceDTO;
use Application\Round\DTOs\PaceResultDTO;
use Domain\Cart\ValueObjects\CartId;
use Domain\Course\ValueObjects\CourseId;
use Domain\Round\Events\SlowPlayDetectedEvent;
use Domain\Round\Exceptions\RoundNotFoundException;
use Domain\Round\Repositories\RoundRepositoryInterface;
use Domain\Round\Services\PaceCalculator;
use Domain\Round\ValueObjects\ActualTime;
use Domain\Round\ValueObjects\PaceStatus;
use Domain\Round\ValueObjects\ParTime;
use Domain\Round\ValueObjects\RoundId;
use Domain\Shared\Events\DomainEventDispatcher;

// SOLID SRP: load the round, ask the pace service, publish SlowPlayDetectedEvent when behind.
final class CalculatePaceUseCase
{
    public function __construct(
        private readonly RoundRepositoryInterface $rounds,
        private readonly PaceCalculator $pace,
        private readonly DomainEventDispatcher $events,
    ) {}

    public function execute(CalculatePaceDTO $dto): PaceResultDTO
    {
        $roundId = new RoundId($dto->roundId);
        $round = $this->rounds->findById($roundId);

        if ($round === null) {
            throw RoundNotFoundException::forId($roundId);
        }

        $status = $this->pace->calculate(
            new ActualTime($dto->actualSeconds),
            new ParTime($dto->parSeconds),
        );

        $round->pace_status = $status;
        $this->rounds->save($round);

        if ($status !== PaceStatus::OnPace) {
            $this->events->dispatch(new SlowPlayDetectedEvent(
                new CourseId((int) $round->course_id),
                new CartId((int) $round->cart_id),
                $roundId,
                $status,
            ));
        }

        return new PaceResultDTO($roundId->value, $status);
    }
}
