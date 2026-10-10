<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use Application\Cart\DTOs\GetLiveCartsDTO;
use Application\Cart\DTOs\IngestGpsDTO;
use Application\Cart\UseCases\GetLiveCartsUseCase;
use Application\Cart\UseCases\IngestGpsPositionUseCase;
use Domain\Cart\Events\CartMovedEvent;
use PHPUnit\Framework\TestCase;
use Tests\Support\ImmediateTransactionRunner;
use Tests\Support\InMemoryCartRepository;
use Tests\Support\InMemoryPositionRepository;
use Tests\Support\RecordingEventDispatcher;

final class GpsUseCaseTest extends TestCase
{
    public function test_ingest_stores_history_and_the_live_position_then_dispatches_cart_moved(): void
    {
        $carts = new InMemoryCartRepository();
        $positions = new InMemoryPositionRepository();
        $events = new RecordingEventDispatcher();
        $ingest = new IngestGpsPositionUseCase($carts, $positions, $events, new ImmediateTransactionRunner());

        $result = $ingest->execute(new IngestGpsDTO(7, 1, -33.9249, 18.4241, 3.5, 4));

        $this->assertSame(7, $result->cartId);
        $this->assertSame(1, $result->courseId);

        $this->assertCount(1, $positions->appended);
        $this->assertSame(4, $positions->appended[0]['hole']);
        $this->assertEqualsWithDelta(-33.9249, $positions->appended[0]['latitude'], 0.00001);

        $live = (new GetLiveCartsUseCase($carts))->execute(new GetLiveCartsDTO(1));
        $this->assertCount(1, $live->carts);
        $this->assertSame(7, $live->carts[0]->cartId);
        $this->assertEqualsWithDelta(3.5, $live->carts[0]->speed, 0.001);
        $this->assertInstanceOf(CartMovedEvent::class, $events->events[0]);
    }

    public function test_invalid_gps_does_not_store_a_position(): void
    {
        $positions = new InMemoryPositionRepository();
        $ingest = new IngestGpsPositionUseCase(
            new InMemoryCartRepository(),
            $positions,
            new RecordingEventDispatcher(),
            new ImmediateTransactionRunner(),
        );

        try {
            $ingest->execute(new IngestGpsDTO(7, 1, 120, 18.4, 3.5, 4));
            $this->fail('Invalid latitude was accepted.');
        } catch (\InvalidArgumentException) {
        }

        $this->assertSame([], $positions->appended);
    }

    public function test_live_carts_are_empty_when_the_course_has_none(): void
    {
        $result = (new GetLiveCartsUseCase(new InMemoryCartRepository()))->execute(new GetLiveCartsDTO(1));

        $this->assertSame([], $result->carts);
    }
}
