import assert from 'node:assert/strict';
import test from 'node:test';
import { createCheckGeofenceUseCase } from '../src/application/geofence/CheckGeofenceUseCase.js';
import { createApp } from '../src/presentation/http/createApp.js';
import { createHttpController } from '../src/presentation/http/createHttpController.js';
import { registerSocketHandlers } from '../src/presentation/websocket/socketHandler.js';
import { CAPE_TOWN_COURSES, startGpsSimulator } from '../src/infrastructure/simulator/gpsSimulator.js';

function listen(app) {
  return new Promise((resolve) => {
    const server = app.listen(0, '127.0.0.1', () => resolve(server));
  });
}

test('POST /ingest rejects an impossible latitude before the use case', async () => {
  let called = false;
  const app = createApp({
    config: { redisHost: 'redis', laravelApi: 'http://laravel:8000' },
    controller: createHttpController({
      ingestGps: {
        async execute() {
          called = true;
        },
      },
      checkGeofence: createCheckGeofenceUseCase(),
    }),
  });
  const server = await listen(app);

  try {
    const response = await fetch(`http://127.0.0.1:${server.address().port}/ingest`, {
      method: 'POST',
      headers: { 'content-type': 'application/json' },
      body: JSON.stringify({
        cart_id: 7,
        course_id: 1,
        lat: 91,
        lng: 18.4,
        speed: 1,
        hole: 1,
      }),
    });
    const body = await response.json();

    assert.equal(response.status, 422);
    assert.match(body.message, /lat/);
    assert.equal(called, false);
  } finally {
    server.close();
  }
});

test('POST /geofence/check answers from the ray cast', async () => {
  const app = createApp({
    config: { redisHost: 'redis', laravelApi: 'http://laravel:8000' },
    controller: createHttpController({
      ingestGps: { async execute() {} },
      checkGeofence: createCheckGeofenceUseCase(),
    }),
  });
  const server = await listen(app);

  try {
    const response = await fetch(`http://127.0.0.1:${server.address().port}/geofence/check`, {
      method: 'POST',
      headers: { 'content-type': 'application/json' },
      body: JSON.stringify({
        lat: 1,
        lng: 1,
        polygon: [
          { lat: 0, lng: 0 },
          { lat: 0, lng: 2 },
          { lat: 2, lng: 2 },
          { lat: 2, lng: 0 },
        ],
      }),
    });

    assert.equal(response.status, 200);
    assert.deepEqual(await response.json(), { inside: true });
  } finally {
    server.close();
  }
});

test('socket handler joins a course room and relays a marshal message', () => {
  const emitted = [];
  const joined = [];
  const rooms = new Set();
  const io = {
    on(event, handler) {
      assert.equal(event, 'connection');
      const socket = {
        rooms,
        on(name, listener) {
          socket[name] = listener;
        },
        join(room) {
          rooms.add(room);
          joined.push(room);
        },
        leave(room) {
          rooms.delete(room);
        },
      };
      handler(socket);
      socket['join:course'](1);
      socket['join:course'](2);
      socket['marshal:message']({ course_id: 1, message: '  Hold at the tee  ' });
    },
    to(room) {
      return {
        emit(event, payload) {
          emitted.push({ room, event, payload });
        },
      };
    },
  };

  registerSocketHandlers(io);

  assert.deepEqual(joined, ['course:1', 'course:2']);
  assert.deepEqual([...rooms], ['course:2']);
  assert.equal(emitted[0].room, 'course:1');
  assert.equal(emitted[0].event, 'marshal:message');
  assert.equal(emitted[0].payload.message, 'Hold at the tee');
});

test('simulator posts thirty carts at Rondebosch Golf Club', () => {
  const calls = [];
  const stop = startGpsSimulator({
    port: 3000,
    cartCount: 30,
    intervalMs: 60_000,
    random: () => 0.5,
    fetchImpl: async (url, options) => {
      calls.push({ url, body: JSON.parse(options.body) });
      return { ok: true };
    },
  });

  try {
    assert.equal(calls.length, 30);
    assert.equal(calls[0].url, 'http://127.0.0.1:3000/ingest');
    assert.deepEqual(calls.map((call) => call.body.cart_id), Array.from({ length: 30 }, (_v, i) => i + 1));
    assert.equal(calls[0].body.course_id, 1);
    assert.ok(Math.abs(calls[0].body.lat + 33.956) < 0.01);
    assert.ok(Math.abs(calls[0].body.lng - 18.4943) < 0.01);
    assert.equal(calls[25].body.actual_seconds, 115);
    assert.equal(calls[29].body.actual_seconds, 130);
  } finally {
    stop();
  }
});

test('simulator posts a fleet for each Cape Town course', () => {
  const calls = [];
  const stop = startGpsSimulator({
    port: 3000,
    cartCount: 2,
    intervalMs: 60_000,
    courses: CAPE_TOWN_COURSES,
    random: () => 0.5,
    fetchImpl: async (_url, options) => {
      calls.push(JSON.parse(options.body));
      return { ok: true };
    },
  });

  try {
    assert.deepEqual([...new Set(calls.map((call) => call.course_id))], [1, 2, 3, 4]);
    assert.equal(calls.length, 8);
    assert.ok(Math.abs(calls.find((call) => call.course_id === 3).lat + 33.8617) < 0.01);
  } finally {
    stop();
  }
});
