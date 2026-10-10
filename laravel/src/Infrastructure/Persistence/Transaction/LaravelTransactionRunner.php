<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Transaction;

use Application\Shared\TransactionRunner;
use Illuminate\Database\ConnectionResolverInterface;

// Infrastructure adapter. Rolls the database work back when the callback throws.
final class LaravelTransactionRunner implements TransactionRunner
{
    public function __construct(private readonly ConnectionResolverInterface $connections) {}

    public function run(callable $work): mixed
    {
        return $this->connections->connection()->transaction($work);
    }
}
