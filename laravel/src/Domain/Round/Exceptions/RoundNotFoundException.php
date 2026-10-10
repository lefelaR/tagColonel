<?php

declare(strict_types=1);

namespace Domain\Round\Exceptions;

use Domain\Round\ValueObjects\RoundId;

final class RoundNotFoundException extends \RuntimeException
{
    public static function forId(RoundId $id): self
    {
        return new self('Round '.$id->value.' was not found.');
    }
}
