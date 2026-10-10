<?php

declare(strict_types=1);

namespace Application\Cart\DTOs;

final readonly class IngestGpsResultDTO
{
    public function __construct(
        public int $cartId,
        public int $courseId,
        public float $lat,
        public float $lng,
    ) {}
}
