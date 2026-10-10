<?php

declare(strict_types=1);

namespace Application\Cart\DTOs;

final readonly class LiveCartsResultDTO
{
    /** @param list<LiveCartDTO> $carts */
    public function __construct(public array $carts) {}
}
