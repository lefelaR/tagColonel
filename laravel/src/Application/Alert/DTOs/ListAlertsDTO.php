<?php

declare(strict_types=1);

namespace Application\Alert\DTOs;

final readonly class ListAlertsDTO
{
    public function __construct(public int $courseId) {}
}
