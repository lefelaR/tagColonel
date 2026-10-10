<?php

declare(strict_types=1);

namespace Tests\Unit\Application;

use Application\Alert\DTOs\ListAlertsDTO;
use Application\Alert\Listeners\CreateAlertOnSlowPlay;
use Application\Alert\UseCases\CreateAlertUseCase;
use Application\Alert\UseCases\ListAlertsUseCase;
use Domain\Cart\ValueObjects\CartId;
use Domain\Course\ValueObjects\CourseId;
use Domain\Round\Events\SlowPlayDetectedEvent;
use Domain\Round\ValueObjects\PaceStatus;
use Domain\Round\ValueObjects\RoundId;
use PHPUnit\Framework\TestCase;
use Tests\Support\InMemoryAlertRepository;

final class AlertUseCaseTest extends TestCase
{
    public function test_slow_play_listener_creates_an_unresolved_alert(): void
    {
        $alerts = new InMemoryAlertRepository();
        $listener = new CreateAlertOnSlowPlay(new CreateAlertUseCase($alerts));

        $listener->handle(new SlowPlayDetectedEvent(
            new CourseId(3),
            new CartId(9),
            new RoundId(4),
            PaceStatus::VerySlow,
        ));

        $listed = (new ListAlertsUseCase($alerts))->execute(new ListAlertsDTO(3));

        $this->assertCount(1, $listed->alerts);
        $this->assertSame(9, $listed->alerts[0]->cartId);
        $this->assertSame(4, $listed->alerts[0]->roundId);
        $this->assertSame('very_slow', $listed->alerts[0]->paceStatus);
        $this->assertSame('Slow play detected.', $listed->alerts[0]->message);
        $this->assertSame([], (new ListAlertsUseCase($alerts))->execute(new ListAlertsDTO(8))->alerts);
    }
}
