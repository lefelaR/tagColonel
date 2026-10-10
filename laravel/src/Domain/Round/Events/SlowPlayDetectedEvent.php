<?php

declare(strict_types=1);

namespace Domain\Round\Events;

use Domain\Cart\ValueObjects\CartId;
use Domain\Course\ValueObjects\CourseId;
use Domain\Round\ValueObjects\PaceStatus;
use Domain\Round\ValueObjects\RoundId;

// DDD: domain event. Raised when pace is Slow or VerySlow, not when the cart is on pace.
final readonly class SlowPlayDetectedEvent
{
    public function __construct(
        public CourseId $courseId,
        public CartId $cartId,
        public RoundId $roundId,
        public PaceStatus $paceStatus,
    ) {}
}
