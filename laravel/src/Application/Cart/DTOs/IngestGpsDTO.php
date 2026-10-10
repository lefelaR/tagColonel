<?php

declare(strict_types=1);

namespace Application\Cart\DTOs;

// Application boundary. Primitives stop here; the use case builds value objects.
final readonly class IngestGpsDTO
{
    public function __construct(
        public int $cartId,
        public int $courseId,
        public float $lat,
        public float $lng,
        public float $speed,
        public int $hole,
    ) {}
}
