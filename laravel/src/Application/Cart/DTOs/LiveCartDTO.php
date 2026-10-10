<?php

declare(strict_types=1);

namespace Application\Cart\DTOs;

final readonly class LiveCartDTO
{
    public function __construct(
        public int $cartId,
        public float $latitude,
        public float $longitude,
        public float $speed,
    ) {}
}
