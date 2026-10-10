<?php

declare(strict_types=1);

namespace Application\Alert\UseCases;

use Application\Alert\DTOs\AlertDTO;
use Application\Alert\DTOs\CreateAlertDTO;
use Domain\Alert\Models\PaceAlert;
use Domain\Alert\Repositories\AlertRepositoryInterface;
use Domain\Cart\ValueObjects\CartId;
use Domain\Course\ValueObjects\CourseId;
use Domain\Round\ValueObjects\PaceStatus;
use Domain\Round\ValueObjects\RoundId;

// SOLID SRP: persist one alert. Pace classification stays in PaceCalculationService.
final class CreateAlertUseCase
{
    public function __construct(private readonly AlertRepositoryInterface $alerts) {}

    public function execute(CreateAlertDTO $dto): AlertDTO
    {
        $paceStatus = PaceStatus::tryFrom($dto->paceStatus);

        if ($paceStatus === null) {
            throw new \InvalidArgumentException('Pace status is not recognised.');
        }

        $courseId = new CourseId($dto->courseId);
        $cartId = new CartId($dto->cartId);
        $roundId = $dto->roundId === null ? null : new RoundId($dto->roundId);

        $alert = new PaceAlert([
            'course_id' => $courseId->value,
            'cart_id' => $cartId->value,
            'round_id' => $roundId?->value,
            'pace_status' => $paceStatus,
            'message' => $dto->message,
        ]);

        $this->alerts->save($alert);

        return new AlertDTO(
            (int) $alert->id,
            $courseId->value,
            $cartId->value,
            $roundId?->value,
            $paceStatus->value,
            $dto->message,
        );
    }
}
