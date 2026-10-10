<?php

declare(strict_types=1);

namespace Presentation\Http\Controllers;

use Application\Cart\DTOs\GetLiveCartsDTO;
use Application\Cart\UseCases\GetLiveCartsUseCase;
use Application\Cart\UseCases\IngestGpsPositionUseCase;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Presentation\Http\Requests\IngestGpsRequest;
use Presentation\Http\Resources\CartResource;
use Presentation\Http\Resources\IngestGpsResource;

// Presentation only: Request -> DTO -> UseCase -> Resource. No Cart::where() here.
final class CartController
{
    public function live(int $id, GetLiveCartsUseCase $live): AnonymousResourceCollection
    {
        return CartResource::collection($live->execute(new GetLiveCartsDTO($id))->carts);
    }

    public function ingest(IngestGpsRequest $request, IngestGpsPositionUseCase $ingest): IngestGpsResource
    {
        return new IngestGpsResource($ingest->execute($request->toDto()));
    }
}
