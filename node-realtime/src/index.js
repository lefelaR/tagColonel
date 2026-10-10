import { createClient } from 'redis';
import axios from 'axios';
import { createCheckGeofenceUseCase } from './application/geofence/CheckGeofenceUseCase.js';
import { createIngestGpsUseCase } from './application/ingest/IngestGpsUseCase.js';
import { loadConfig } from './infrastructure/config/loadConfig.js';
import { createLaravelApiClient } from './infrastructure/laravel/LaravelApiClient.js';
import { createLiveCartRedisRepo } from './infrastructure/redis/LiveCartRedisRepo.js';
import { CAPE_TOWN_COURSES, startGpsSimulator } from './infrastructure/simulator/gpsSimulator.js';
import { createApp } from './presentation/http/createApp.js';
import { createHttpController } from './presentation/http/createHttpController.js';
import { createSocketBroadcaster } from './presentation/websocket/createSocketBroadcaster.js';
import { createSocketServer } from './presentation/websocket/createSocketServer.js';

// Composition root: wire config, Redis, Laravel, HTTP, and Socket.io. No business rules here.
const config = loadConfig(process.env);
const redis = createClient({ url: `redis://${config.redisHost}:${config.redisPort}` });
redis.on('error', (error) => {
  console.error('Redis client error', error);
});
await redis.connect();

const liveCarts = createLiveCartRedisRepo(redis);
const laravelApi = createLaravelApiClient({ baseUrl: config.laravelApi, http: axios });
const { httpServer: socketServer, io } = createSocketServer();
const ingestGps = createIngestGpsUseCase({
  liveCarts,
  laravelApi,
  broadcaster: createSocketBroadcaster(io),
});
const app = createApp({
  config,
  controller: createHttpController({
    ingestGps,
    checkGeofence: createCheckGeofenceUseCase(),
  }),
});

const httpServer = app.listen(config.port, '0.0.0.0');
socketServer.listen(config.socketPort, '0.0.0.0');

let stopSimulator = () => {};

if (config.simulatorEnabled) {
  stopSimulator = startGpsSimulator({
    port: config.port,
    cartCount: config.simulatorCarts,
    intervalMs: config.simulatorIntervalMs,
    courses: CAPE_TOWN_COURSES,
  });
}

function shutdown() {
  stopSimulator();
  httpServer.close();
  socketServer.close();
  redis.quit().catch((error) => {
    console.error('Redis quit failed', error);
  });
}

process.on('SIGTERM', shutdown);
process.on('SIGINT', shutdown);
