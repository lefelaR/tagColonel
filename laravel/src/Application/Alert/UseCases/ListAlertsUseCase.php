<?php

declare(strict_types=1);

namespace Application\Alert\UseCases;

use Application\Alert\DTOs\AlertDTO;
use Application\Alert\DTOs\AlertListResultDTO;
use Application\Alert\DTOs\ListAlertsDTO;
use Domain\Alert\Models\PaceAlert;
use Domain\Alert\Repositories\AlertRepositoryInterface;
use Domain\Course\ValueObjects\CourseId;

// SOLID SRP: read unresolved alerts for one course and map them to DTOs.
final class ListAlertsUseCase
{
    public function __construct(private readonly AlertRepositoryInterface $alerts) {}

    public function execute(ListAlertsDTO $dto): AlertListResultDTO
    {
        $models = $this->alerts->findUnresolvedByCourse(new CourseId($dto->courseId));
        $alerts = [];

        foreach ($models as $model) {
            $alerts[] = $this->toDto($model);
        }

        return new AlertListResultDTO($alerts);
    }

    private function toDto(PaceAlert $alert): AlertDTO
    {
        return new AlertDTO(
            (int) $alert->id,
            (int) $alert->course_id,
            (int) $alert->cart_id,
            $alert->round_id === null ? null : (int) $alert->round_id,
            $alert->pace_status->value,
            $alert->message,
        );
    }
}
