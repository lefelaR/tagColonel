<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use Application\Round\DTOs\CalculatePaceDTO;
use Application\Round\UseCases\CalculatePaceUseCase;
use Domain\Round\Events\SlowPlayDetectedEvent;
use Domain\Round\Exceptions\RoundNotFoundException;
use Domain\Round\Models\Round;
use Domain\Round\Services\PaceCalculationService;
use Domain\Round\ValueObjects\PaceStatus;
use Domain\Round\ValueObjects\RoundId;
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryRoundRepository;
use Tests\Support\RecordingEventDispatcher;

final class CalculatePaceUseCaseTest extends TestCase
{
    public function test_slow_pace_is_saved_and_publishes_slow_play_detected(): void
    {
        $rounds = $this->roundsWith(4, 3, 9);
        $events = new RecordingEventDispatcher();
        $useCase = $this->useCase($rounds, $events);

        $result = $useCase->execute(new CalculatePaceDTO(4, 115, 100));

        $this->assertSame(PaceStatus::Slow, $result->paceStatus);
        $this->assertSame(PaceStatus::Slow, $rounds->findById(new RoundId(4))?->pace_status);
        $this->assertInstanceOf(SlowPlayDetectedEvent::class, $events->events[0]);
        $this->assertSame(PaceStatus::Slow, $events->events[0]->paceStatus);
        $this->assertSame(9, $events->events[0]->cartId->value);
    }

    public function test_on_pace_does_not_publish_slow_play(): void
    {
        $rounds = $this->roundsWith(4, 3, 9);
        $events = new RecordingEventDispatcher();

        $result = $this->useCase($rounds, $events)->execute(new CalculatePaceDTO(4, 100, 100));

        $this->assertSame(PaceStatus::OnPace, $result->paceStatus);
        $this->assertSame([], $events->events);
    }

    public function test_missing_round_is_rejected(): void
    {
        $useCase = $this->useCase(new InMemoryRoundRepository(), new RecordingEventDispatcher());

        $this->expectException(RoundNotFoundException::class);

        $useCase->execute(new CalculatePaceDTO(4, 100, 100));
    }

    private function useCase(InMemoryRoundRepository $rounds, RecordingEventDispatcher $events): CalculatePaceUseCase
    {
        return new CalculatePaceUseCase($rounds, new PaceCalculationService(), $events);
    }

    private function roundsWith(int $roundId, int $courseId, int $cartId): InMemoryRoundRepository
    {
        $round = new Round([
            'course_id' => $courseId,
            'cart_id' => $cartId,
        ]);
        $round->id = $roundId;

        $rounds = new InMemoryRoundRepository();
        $rounds->save($round);

        return $rounds;
    }
}
