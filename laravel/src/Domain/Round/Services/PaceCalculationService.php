<?php

declare(strict_types=1);

namespace Domain\Round\Services;

use Domain\Round\ValueObjects\ActualTime;
use Domain\Round\ValueObjects\PaceStatus;
use Domain\Round\ValueObjects\ParTime;

// DDD: domain service. Pure pace rule, no framework and no database.
final class PaceCalculationService implements PaceCalculator
{
    public function calculate(ActualTime $actual, ParTime $par): PaceStatus
    {
        if ($actual->seconds <= $par->seconds) {
            return PaceStatus::OnPace;
        }

        $slowLimit = $par->seconds + intdiv($par->seconds * 15, 100);

        if ($actual->seconds <= $slowLimit) {
            return PaceStatus::Slow;
        }

        return PaceStatus::VerySlow;
    }
}
