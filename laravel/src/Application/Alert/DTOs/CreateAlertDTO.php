<?php

declare(strict_types=1);

namespace Application\Alert\DTOs;

final readonly class CreateAlertDTO
{
    public function __construct(
        public int $courseId,
        public int $cartId,
        public ?int $roundId,
        public string $paceStatus,
        public ?string $message,
    ) {}
}
