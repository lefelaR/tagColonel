import assert from 'node:assert/strict';
import test from 'node:test';
import { pointInPolygon } from '../src/domain/geofence/GeofenceService.js';
import { isSlow, paceStatus } from '../src/domain/pace/PaceService.js';

const square = [
  { lat: 0, lng: 0 },
  { lat: 0, lng: 2 },
  { lat: 2, lng: 2 },
  { lat: 2, lng: 0 },
];

test('pace matches the Laravel 15 percent bands', () => {
  assert.equal(paceStatus(100, 100), 'on_pace');
  assert.equal(paceStatus(90, 100), 'on_pace');
  assert.equal(isSlow(90, 100), false);
  assert.equal(paceStatus(115, 100), 'slow');
  assert.equal(isSlow(115, 100), true);
  assert.equal(paceStatus(116, 100), 'very_slow');
});

test('ray cast matches a square inside and outside', () => {
  assert.equal(pointInPolygon({ lat: 1, lng: 1 }, square), true);
  assert.equal(pointInPolygon({ lat: 1, lng: 3 }, square), false);
  assert.equal(pointInPolygon({ lat: 1, lng: -1 }, square), false);
});
