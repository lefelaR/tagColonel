import assert from 'node:assert/strict';
import test from 'node:test';
import { createIngestGpsUseCase } from '../src/application/ingest/IngestGpsUseCase.js';

function fakeLiveCarts() {
  const paces = new Map();
  return {
    saved: [],
    published: [],
    async saveLivePosition(position) {
      this.saved.push(position);
    },
    async getPaceStatus(courseId, cartId) {
      return paces.get(`${courseId}:${cartId}`) ?? null;
    },
    async savePaceStatus(courseId, cartId, status) {
      paces.set(`${courseId}:${cartId}`, status);
    },
    async publish(courseId, payload) {
      this.published.push({ courseId, payload });
    },
  };
}

function dto(overrides = {}) {
  return {
    cartId: 7,
    courseId: 1,
    lat: -33.9249,
    lng: 18.4241,
    speed: 3.5,
    hole: 4,
    actualSeconds: 130,
    parSeconds: 100,
    ...overrides,
  };
}

test('ingest stores the live point, publishes it, and alerts once when pace worsens', async () => {
  const liveCarts = fakeLiveCarts();
  const alerts = [];
  const events = [];
  const ingest = createIngestGpsUseCase({
    liveCarts,
    laravelApi: {
      async createAlert(alert) {
        alerts.push(alert);
      },
    },
    broadcaster: {
      emitCartUpdate(courseId, update) {
        events.push({ type: 'cart:update', courseId, update });
      },
      emitMarshalMessage(courseId, message) {
        events.push({ type: 'marshal:message', courseId, message });
      },
    },
  });

  const first = await ingest.execute(dto());
  const second = await ingest.execute(dto());

  assert.equal(first.pace_status, 'very_slow');
  assert.equal(first.alert_sent, true);
  assert.equal(second.alert_sent, false);
  assert.equal(alerts.length, 1);
  assert.equal(alerts[0].message, 'Slow play detected.');
  assert.equal(liveCarts.saved.length, 2);
  assert.equal(liveCarts.published[0].courseId, 1);
  assert.equal(events[0].type, 'cart:update');
  assert.equal(events[1].type, 'marshal:message');
  assert.equal(events[1].message.cart_id, 7);
});

test('ingest skips Laravel when the cart is on pace', async () => {
  const liveCarts = fakeLiveCarts();
  let alerts = 0;
  const ingest = createIngestGpsUseCase({
    liveCarts,
    laravelApi: {
      async createAlert() {
        alerts += 1;
      },
    },
    broadcaster: {
      emitCartUpdate() {},
      emitMarshalMessage() {},
    },
  });

  const result = await ingest.execute(dto({ actualSeconds: 90 }));

  assert.equal(result.pace_status, 'on_pace');
  assert.equal(result.alert_sent, false);
  assert.equal(alerts, 0);
});
