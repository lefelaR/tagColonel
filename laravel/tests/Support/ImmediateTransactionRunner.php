<?php

declare(strict_types=1);

namespace Tests\Support;

use Application\Shared\TransactionRunner;

final class ImmediateTransactionRunner implements TransactionRunner
{
    public function run(callable $work): mixed
    {
        return $work();
    }
}
