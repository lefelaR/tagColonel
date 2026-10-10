<?php

declare(strict_types=1);

namespace Application\Cart\UseCases;

use Application\Cart\DTOs\IngestGpsDTO;
use Application\Cart\DTOs\IngestGpsResultDTO;
use Application\Shared\TransactionRunner;
use Domain\Cart\Events\CartMovedEvent;
use Domain\Cart\Repositories\CartRepositoryInterface;
use Domain\Cart\Repositories\PositionRepositoryInterface;
use Domain\Cart\ValueObjects\CartId;
use Domain\Cart\ValueObjects\Hole;
use Domain\Cart\ValueObjects\Speed;
use Domain\Course\ValueObjects\CourseId;
use Domain\Course\ValueObjects\GeoCoordinate;
use Domain\Shared\Events\DomainEventDispatcher;

// SOLID SRP: orchestrate validation, history, and the live index. No pace rules here.
final class IngestGpsPositionUseCase
{
    public function __construct(
        private readonly CartRepositoryInterface $carts,
        private readonly PositionRepositoryInterface $positions,
        private readonly DomainEventDispatcher $events,
        private readonly TransactionRunner $transactions,
    ) {}

    public function execute(IngestGpsDTO $dto): IngestGpsResultDTO
    {
        $cartId = new CartId($dto->cartId);
        $courseId = new CourseId($dto->courseId);
        $coordinate = new GeoCoordinate($dto->lat, $dto->lng);
        $speed = new Speed($dto->speed);
        $hole = new Hole($dto->hole);

        $recordedAt = new \DateTimeImmutable();

        $this->transactions->run(function () use ($cartId, $courseId, $coordinate, $speed, $hole, $recordedAt): void {
            $this->positions->append($cartId, $coordinate, $speed, $hole, $recordedAt);
            $this->carts->saveLivePosition($cartId, $courseId, $coordinate, $speed);
        });

        $this->events->dispatch(new CartMovedEvent($cartId, $courseId, $coordinate, $speed, $hole));

        return new IngestGpsResultDTO($cartId->value, $courseId->value, $coordinate->lat, $coordinate->lng);
    }
}
