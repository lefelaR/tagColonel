<?php

declare(strict_types=1);

namespace Domain\Round\Services;

use Domain\Round\ValueObjects\ActualTime;
use Domain\Round\ValueObjects\PaceStatus;
use Domain\Round\ValueObjects\ParTime;

// SOLID DIP: CalculatePaceUseCase depends on this port, not a concrete policy.
interface PaceCalculator
{
    public function calculate(ActualTime $actual, ParTime $par): PaceStatus;
}
