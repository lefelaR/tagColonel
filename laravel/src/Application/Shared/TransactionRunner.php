<?php

declare(strict_types=1);

namespace Application\Shared;

// SOLID DIP: use cases run a transaction without calling the database facade.
interface TransactionRunner
{
    public function run(callable $work): mixed;
}
