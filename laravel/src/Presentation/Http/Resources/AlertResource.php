<?php

declare(strict_types=1);

namespace Presentation\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Application\Alert\DTOs\AlertDTO */
final class AlertResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->courseId,
            'cart_id' => $this->cartId,
            'round_id' => $this->roundId,
            'pace_status' => $this->paceStatus,
            'message' => $this->message,
        ];
    }

    public function withResponse(Request $request, \Symfony\Component\HttpFoundation\Response $response): void
    {
        if ($request->isMethod('POST')) {
            $response->setStatusCode(201);
        }
    }
}
