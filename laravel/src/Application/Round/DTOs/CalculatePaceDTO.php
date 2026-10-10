<?php

declare(strict_types=1);

namespace Application\Round\DTOs;

final readonly class CalculatePaceDTO
{
    public function __construct(
        public int $roundId,
        public int $actualSeconds,
        public int $parSeconds,
    ) {}
}
