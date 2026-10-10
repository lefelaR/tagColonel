<?php

declare(strict_types=1);

namespace Tests\Feature;

use Application\Cart\UseCases\GetLiveCartsUseCase;
use Application\Cart\UseCases\IngestGpsPositionUseCase;
use Application\Round\UseCases\CalculatePaceUseCase;
use Domain\Round\Events\SlowPlayDetectedEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Tests\TestCase;

final class UseCaseBindingTest extends TestCase
{
    public function test_use_cases_resolve_through_interfaces_and_slow_play_is_listened_for(): void
    {
        $this->assertInstanceOf(IngestGpsPositionUseCase::class, $this->app->make(IngestGpsPositionUseCase::class));
        $this->assertInstanceOf(GetLiveCartsUseCase::class, $this->app->make(GetLiveCartsUseCase::class));
        $this->assertInstanceOf(CalculatePaceUseCase::class, $this->app->make(CalculatePaceUseCase::class));
        $this->assertNotEmpty($this->app->make(Dispatcher::class)->getListeners(SlowPlayDetectedEvent::class));
    }
}
