
# Cursor Prompts Library — Copy-Paste

## Prompt 1 — Bootstrap
```
Read .cursor/rules/fairwayflow-ddd-solid.mdc and CURSOR_BUILD_PLAN.md Phase 1. Generate docker-compose.yml + Dockerfiles. Enforce SOLID.
```

## Prompt 2 — Laravel DDD
```
Phase 2: Create DDD structure. ValueObjects: GeoCoordinate, Polygon, CourseId, CartId, Speed. Repositories Interfaces + Eloquent + Redis impl. Bind in RepositoryServiceProvider. No business logic in models.
```

## Prompt 3 — UseCases
```
Phase 3: Application UseCases. IngestGpsPositionUseCase, GetLiveCartsUseCase, CalculatePaceUseCase. Each has single execute(DTO). Inject interfaces only. Add domain events SlowPlayDetectedEvent.
```

## Prompt 4 — Domain Services
```
Create Domain Services: GeofenceService.isInside(point, polygon) with ray casting algorithm pure PHP, PaceCalculationService.calculate(actual, par): PaceStatus enum. Must be unit testable, no facades.
```

## Prompt 5 — Controllers
```
Phase 4: Thin controllers max 30 lines. Validate Request -> DTO -> UseCase -> Resource. If you write Cart::where() in controller, you failed.
```

## Prompt 6 — Node Service
```
Phase 5: Node.js DDD Lite. IngestGpsUseCase, LiveCartRedisRepo, LaravelApiClient. Express controller thin. Socket.io handler. GPS simulator 30 carts.
```

## Prompt 7 — Vue
```
Phase 6: Vue 3 composables: useLiveCarts, usePaceAlerts. MapView with Leaflet. Pinia store only state. Services for API calls.
```

## Prompt 8 — Polish
```
Phase 7: Seed 1 course 30 carts, README with mermaid architecture, demo script, docker compose up must work.
```
