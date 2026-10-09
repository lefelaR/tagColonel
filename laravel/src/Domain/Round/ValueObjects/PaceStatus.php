<?php

declare(strict_types=1);

namespace Domain\Round\ValueObjects;

enum PaceStatus: string
{
    case OnPace = 'on_pace';
    case Slow = 'slow';
    case VerySlow = 'very_slow';
}
