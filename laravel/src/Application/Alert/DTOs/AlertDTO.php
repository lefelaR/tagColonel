<?php

declare(strict_types=1);

namespace Application\Alert\DTOs;

final readonly class AlertDTO
{
    public function __construct(
        public int $id,
        public int $courseId,
        public int $cartId,
        public ?int $roundId,
        public string $paceStatus,
        public ?string $message,
    ) {}
}
