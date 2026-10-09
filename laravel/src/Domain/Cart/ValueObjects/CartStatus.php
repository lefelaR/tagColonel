<?php

declare(strict_types=1);

namespace Domain\Cart\ValueObjects;

enum CartStatus: string
{
    case Available = 'available';
    case InRound = 'in_round';
    case Offline = 'offline';
}
