<?php

declare(strict_types=1);

namespace Presentation\Http\Controllers;

use Application\Alert\UseCases\CreateAlertUseCase;
use Application\Alert\UseCases\ListAlertsUseCase;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Presentation\Http\Requests\ListAlertsRequest;
use Presentation\Http\Requests\StoreAlertRequest;
use Presentation\Http\Resources\AlertResource;

// Presentation only: Request -> DTO -> UseCase -> Resource. No PaceAlert::query() here.
final class AlertController
{
    public function index(ListAlertsRequest $request, ListAlertsUseCase $list): AnonymousResourceCollection
    {
        return AlertResource::collection($list->execute($request->toDto())->alerts);
    }

    public function store(StoreAlertRequest $request, CreateAlertUseCase $create): AlertResource
    {
        return new AlertResource($create->execute($request->toDto()));
    }
}
