<?php

declare(strict_types=1);

namespace Application\Round\DTOs;

use Domain\Round\ValueObjects\PaceStatus;

final readonly class PaceResultDTO
{
    public function __construct(
        public int $roundId,
        public PaceStatus $paceStatus,
    ) {}
}
