<?php

declare(strict_types=1);

namespace Application\Cart\DTOs;

final readonly class GetLiveCartsDTO
{
    public function __construct(public int $courseId) {}
}
