
# FairwayFlow — Cursor Build Plan (Tagmarshal Interview Demo)
# Stack: Laravel 11 (DDD+SOLID) + Node.js 20 + Vue 3 + MySQL + Redis + Docker

## Goal
Build a dockerized mini-Tagmarshal: 30 golf carts sending GPS every 3 sec, real-time map, pace alerts, geofencing. Use this as live demo for Tagmarshal second round (they use Laravel heavy + Node + Vue/React). Project must scream "Tech Lead level architecture".

## Phase 0 — Setup Cursor (5 min)

1. Open Cursor IDE → Open folder `fairwayflow`
2. Ensure `.cursor/rules/fairwayflow-ddd-solid.mdc` exists — Cursor will auto-read it
3. Create `.env` from `.env.example` if any
4. Run `docker compose up --build` to verify skeleton boots (even empty)

## Phase 1 — Infrastructure Skeleton (Prompt for Cursor)

Copy-paste this into Cursor Chat (Ctrl+L) with Agent mode:

```
You are building FairwayFlow per .cursor/rules/fairwayflow-ddd-solid.mdc (DDD + SOLID enforced).

Task Phase 1: Create docker-compose.yml and all Dockerfiles:
- mysql:8.0, redis:7-alpine, laravel (php:8.3-fpm + composer), node-realtime (node:20-alpine), vue-dashboard (node:20-alpine)
- Laravel serves on 8000, Node API 3000 + Socket.io 3001, Vue 5173
- All services depGenerate docker-compose.yml at root, Dockerfiles in each service
end correctly, env vars: DB_HOST=mysql, REDIS_HOST=redis, LARAVEL_API=http://laravel:8000
- 
Follow SOLID: no hardcoded secrets, use env.
```

## Phase 2 — Laravel DDD Foundation (Most Important for Interview)

Prompt:

```
Phase 2: Laravel DDD structure per rule file.

1. Create src/Domain, src/Application, src/Infrastructure, src/Presentation folders inside laravel/
2. Implement ValueObjects:
   - Domain/Course/ValueObjects/CourseId, GeoCoordinate(lat,lng with validation), Polygon (array of GeoCoordinate + contains() method using ray casting)
   - Domain/Cart/ValueObjects/CartId, Speed, CartStatus enum
   - Domain/Round/ValueObjects/PaceStatus enum (OnPace, Slow, VerySlow)

3. Create Domain Models (Eloquent but in Domain/Models) with only fillable and casts:
   Course, GolfCart, Round, Position, Geofence, PaceAlert

4. Create Repository Interfaces in Domain:
   CourseRepositoryInterface, CartRepositoryInterface (with findLiveByCourse(CourseId): array), PositionRepositoryInterface, AlertRepositoryInterface

5. Implement in Infrastructure/Persistence/Eloquent and Redis:
   CourseEloquentRepository, LiveCartRedisRepository (uses GEOADD, GEORADIUS), PositionEloquentRepository

6. Bind in Infrastructure/Providers/RepositoryServiceProvider

Enforce DIP: UseCases must depend on interfaces only.
```

## Phase 3 — Laravel UseCases (Application Layer)

Prompt:

```
Phase 3: Application UseCases (thin orchestration, SOLID SRP).

Create DTOs and UseCases:

Application/Cart/DTOs: IngestGpsDTO(cartId, courseId, lat, lng, speed, hole), LiveCartDTO
Application/Cart/UseCases:
  - IngestGpsPositionUseCase: inject CartRepositoryInterface + PositionRepositoryInterface + EventDispatcher, execute(IngestGpsDTO) => validates via ValueObjects, saves position, dispatches CartMovedEvent
  - GetLiveCartsUseCase: reads from LiveCartRedisRepository

Application/Round/UseCases:
  - CalculatePaceUseCase: inject RoundRepository + PaceCalculationService (Domain Service), returns PaceStatus

Application/Alert/UseCases:
  - CreateAlertUseCase, ListAlertsUseCase

Each UseCase: single public execute() method, transactional if needed.

Domain Services (pure, no framework):
  Domain/Geofence/Services/GeofenceService: isInside(GeoCoordinate, Polygon): bool
  Domain/Round/Services/PaceCalculationService: calculate(ActualTime, ParTime): PaceStatus

Add domain events: SlowPlayDetectedEvent, GeofenceBreachedEvent
```

## Phase 4 — Laravel Presentation (Thin Controllers)

Prompt:

```
Phase 4: Presentation - Thin Controllers + API Resources + Requests.

Controllers in src/Presentation/Http/Controllers, max 30 lines:
- CourseController@index -> calls ListCoursesUseCase
- CartController@live -> calls GetLiveCartsUseCase returns CartResource collection
- CartController@ingest -> validates IngestGpsRequest (lat,lng,speed rules) -> maps to IngestGpsDTO -> calls IngestGpsPositionUseCase
- AlertController@index, store

Resources: CartResource, AlertResource, CourseResource
Requests: IngestGpsRequest with validation rules (lat between -90,90 etc)

Routes in routes/api.php: /api/courses, /api/courses/{id}/carts/live, /api/ingest, /api/alerts

No business logic in controller - if you see Cart::where(), you violated rule.
```

## Phase 5 — Node.js Real-time Service (DDD Lite)

Prompt:

```
Phase 5: Node.js service per .cursor/rules (DDD Lite + SOLID).

Structure:
src/domain/pace/PaceService.js (pure function isSlow(current, parTime))
src/domain/geofence/GeofenceService.js (pointInPolygon)
src/application/ingest/IngestGpsUseCase.js (inject redisRepo, laravelClient)
src/infrastructure/redis/LiveCartRedisRepo.js
src/infrastructure/laravel/LaravelApiClient.js (axios post to Laravel /api/alerts)
src/presentation/http/app.js (Express thin controller)
src/presentation/websocket/socketHandler.js (join:course, cart:update)

Implement:
POST /ingest: validates, saves to Redis GEO, checks pace, publishes to Redis, emits via Socket.io to course room
POST /geofence/check
Socket.io: on connection -> join:course room, emits cart:update, marshal:message

Add GPS simulator: simulate 30 carts moving random walk near -33.9249,18.4241 every 3 sec calling own /ingest (for demo without hardware)
```

## Phase 6 — Vue 3 Dashboard

Prompt:

```
Phase 6: Vue 3 dashboard (composables + Pinia + SOLID).

Setup: Vite + Vue 3 + TypeScript + Tailwind + Leaflet + socket.io-client + Pinia

Structure:
src/composables/useLiveCarts.ts (socket logic, manages carts ref)
src/composables/usePaceAlerts.ts
src/stores/cartStore.ts (only state, no logic)
src/services/CartService.ts (axios to Laravel)
src/components/MapView.vue (Leaflet map, renders markers from useLiveCarts)
src/components/PaceDashboard.vue (list of carts, color green/yellow/red)
src/components/AlertPanel.vue

App.vue: grid - left 2/3 map, right 1/3 pace + alerts. Connects to Socket.io 3001, joins course 1, displays live.

Add Tailwind, dark theme like Tagmarshal dashboard.
```

## Phase 7 — Wiring + Demo Script

Prompt:

```
Phase 7: Wire all together and create demo script.

1. Update docker-compose to ensure vue can reach node (VITE_SOCKET_URL=http://localhost:3001, VITE_LARAVEL_API=http://localhost:8000)
2. Seed: Create 1 course, 30 carts, 2 geofences (restricted zones)
3. README.md with:
   - Architecture diagram (mermaid)
   - How to run docker compose up --build
   - Interview demo script (2 min)
   - SOLID/DDD explanation for CTO

Demo script for interview:
"At RoomRaccoon I worked Laravel heavy with real-time channel manager updates. For Tagmarshal I built FairwayFlow to show same pattern: Laravel DDD for courses/rounds (domain logic pure, no framework), Node for high-throughput GPS (10k/min), Vue for marshal. Key decision: Redis GEO for live view, MySQL partitioned positions table for history, ValueObjects for GeoCoordinate to prevent invalid lat/lng. Here's 30 carts live, here's slow detection..."

Test: docker compose up, open http://localhost:5173, see 30 carts moving.
```

## Phase 8 — Bonus for Tech Lead (If time before interview)

- Add Tests: Unit test GeofenceService, PaceCalculationService (pure, no DB)
- Add TimescaleDB or MySQL partitioning for positions
- Add JWT auth (Sanctum) for marshal login
- Deploy to Linode: `docker compose` on VPS, or Laravel Cloud
- Add GitHub Actions CI: run tests, lint, build images

## Cursor Commands to Use Daily

- `Ctrl+K` (inline edit): "Refactor this to depend on interface, not concrete"
- `Ctrl+L` (chat): Paste Phase prompts
- Agent Mode: Enable for Phase 2-6
- Use `@codebase` to make Cursor read rule file

## What Tagmarshal CTO Will Look For (Checklist)

- [ ] Controllers thin (<30 lines)
- [ ] Repositories have interfaces
- [ ] ValueObjects validate lat/lng
- [ ] Domain services pure (no DB, no framework)
- [ ] UseCase has single execute()
- [ ] Node service not fat — UseCase pattern
- [ ] Vue composables separate from components
- [ ] Docker compose one command up
- [ ] README explains why Laravel + Node (not just how)

## Final Deliverable for Interview

- GitHub repo: github.com/rakheoanalefela/fairwayflow
- Live URL (Vercel + Linode)
- Loom video 2 min demo (backup if live fails)
- Bring laptop (your new MacBook Pro 2020) with docker compose ready offline

Build time: 5-7 days during garden leave. Start Day 1 today.

