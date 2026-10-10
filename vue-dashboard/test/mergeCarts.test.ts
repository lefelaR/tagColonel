import assert from 'node:assert/strict'
import test from 'node:test'
import { mergeAlerts, mergeLiveCarts, pushAlert, upsertCart } from '../src/domain/mergeCarts.ts'
import type { LiveCart } from '../src/types/dashboard.ts'

const fetched: LiveCart = {
  cartId: 7,
  latitude: -33.9,
  longitude: 18.4,
  speed: 2,
  hole: null,
  paceStatus: null,
}

test('a live refresh keeps pace that arrived on the socket', () => {
  const current = upsertCart([], {
    cart_id: 7,
    course_id: 1,
    lat: -33.92,
    lng: 18.42,
    speed: 3,
    hole: 4,
    pace_status: 'slow',
  })

  const merged = mergeLiveCarts(current, [fetched])

  assert.equal(merged[0].paceStatus, 'slow')
  assert.equal(merged[0].hole, 4)
  assert.equal(merged[0].latitude, -33.9)
})

test('the same marshal message is not listed twice', () => {
  const first = pushAlert([], {
    id: null,
    courseId: 1,
    cartId: 29,
    paceStatus: 'very_slow',
    message: 'Slow play detected.',
  })
  const second = pushAlert(first, first[0])

  assert.equal(second.length, 1)

  const withSaved = mergeAlerts(second, [{
    id: 4,
    courseId: 1,
    cartId: 29,
    paceStatus: 'very_slow',
    message: 'Slow play detected.',
  }])

  assert.equal(withSaved.length, 1)
  assert.equal(withSaved[0].id, 4)
})
