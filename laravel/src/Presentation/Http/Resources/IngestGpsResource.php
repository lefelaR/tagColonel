<?php

declare(strict_types=1);

namespace Presentation\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Application\Cart\DTOs\IngestGpsResultDTO */
final class IngestGpsResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'cart_id' => $this->cartId,
            'course_id' => $this->courseId,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }

    public function withResponse(Request $request, \Symfony\Component\HttpFoundation\Response $response): void
    {
        $response->setStatusCode(201);
    }
}
