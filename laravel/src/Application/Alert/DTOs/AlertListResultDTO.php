<?php

declare(strict_types=1);

namespace Application\Alert\DTOs;

final readonly class AlertListResultDTO
{
    /** @param list<AlertDTO> $alerts */
    public function __construct(public array $alerts) {}
}
