# FairwayFlow

A dockerized mini golf-fleet tracker: four Cape Town courses, thirty carts on each, and a marshal screen that switches between them. Slow play raises an alert.

## Run

```bash
cp .env.example .env
```

Set `APP_KEY` in `.env` to a Laravel key (`php artisan key:generate --show` from the `laravel` directory, or any `base64:` value from `openssl rand -base64 32`).

```bash
docker compose up --build
```

Open http://localhost:5173. The course menu starts on Rondebosch Golf Club. Royal Cape, Milnerton, and Steenberg are the other choices, and each has its own moving carts. Laravel is on http://localhost:8000, the Node ingest API on http://localhost:3000, and Socket.io on http://localhost:3001.

The first boot migrates MySQL and seeds those four courses, thirty carts on each, and two restricted zones per course. The Node simulator then walks every fleet and posts GPS to itself every 3 seconds.

## Architecture

```mermaid
flowchart LR
  subgraph browser [Browser]
    Vue["Vue 3 dashboard :5173"]
  end

  subgraph realtime [Realtime]
    Node["Node ingest + Socket.io :3000 / :3001"]
    Simulator["GPS simulator, four fleets"]
  end

  subgraph platform [Platform]
    Laravel["Laravel API :8000"]
    Redis["Redis GEO"]
    MySQL["MySQL"]
  end

  Simulator --> Node
  Vue -->|cart:update, marshal:message| Node
  Vue -->|live carts and alerts| Laravel
  Node -->|GEOADD + publish| Redis
  Node -->|slow-play alert| Laravel
  Laravel --> Redis
  Laravel --> MySQL
```

Laravel owns the course, the fleet, pace rules, and history. Node owns the high-frequency GPS path and the live socket. Vue is the marshal screen. Redis GEO is the live map. MySQL stores courses, carts, geofences, alerts, and position history.

## Demo script

At RoomRaccoon I worked Laravel heavy with real-time channel manager updates. For Tagmarshal I built FairwayFlow to show the same pattern: Laravel DDD for courses and rounds, with domain logic kept pure and free of the framework; Node for high-throughput GPS; Vue for the marshal. The key decision is Redis GEO for the live view, MySQL for position history, and a GeoCoordinate value object so an invalid latitude or longitude never gets in. The course menu starts on Rondebosch Golf Club and also lists Royal Cape, Milnerton, and Steenberg. Each course shows 30 carts. On Rondebosch, carts 26–28 are slow and 29 and 30 are very slow. The same last-five pattern repeats on the other courses, and each worsening pace creates one alert.

## Why the split

**Laravel, domain first.** Value objects reject bad coordinates, speeds, and hole numbers. `PaceCalculationService` and `GeofenceService` are plain PHP: no facades and no database. Use cases have one `execute` method and depend on repository interfaces. Controllers only validate a request, map a DTO, call a use case, and return a resource.

**Node, DDD lite.** `IngestGpsUseCase` orchestrates. Redis and the Laravel HTTP client are adapters. The Express route and the Socket.io handler do not calculate pace or decide if a point is inside a polygon. The same 15% pace bands and the same ray cast live here so the hot path does not wait on PHP for every fix.

**Vue.** The page loads data. Composables own the socket and the merge rules. Pinia stores carts, alerts, and the connection flag, and nothing else. `CourseService`, `CartService`, and `AlertService` are the only Axios calls. The map, pace list, and alert list receive props.

**Redis GEO versus MySQL.** A live map is a geospatial index that is rewritten every few seconds. History is an append-only positions table indexed by cart and time. Alerts are written once, when a cart gets slower, not on every GPS fix.
