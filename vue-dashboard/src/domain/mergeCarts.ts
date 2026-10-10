import type { CartUpdate, LiveCart, MarshalMessage, PaceAlert, PaceStatus } from '../types/dashboard'

const PACE: readonly PaceStatus[] = ['on_pace', 'slow', 'very_slow']

export function asPace(value: unknown): PaceStatus | null {
  return typeof value === 'string' && PACE.includes(value as PaceStatus) ? value as PaceStatus : null
}

export function cartFromUpdate(update: CartUpdate): LiveCart {
  return {
    cartId: update.cart_id,
    latitude: update.lat,
    longitude: update.lng,
    speed: update.speed,
    hole: update.hole ?? null,
    paceStatus: asPace(update.pace_status),
  }
}

export function mergeLiveCarts(current: LiveCart[], fetched: LiveCart[]): LiveCart[] {
  const byId = new Map(current.map((cart) => [cart.cartId, cart]))

  for (const cart of fetched) {
    const existing = byId.get(cart.cartId)
    byId.set(cart.cartId, {
      ...cart,
      hole: cart.hole ?? existing?.hole ?? null,
      paceStatus: cart.paceStatus ?? existing?.paceStatus ?? null,
    })
  }

  return [...byId.values()].sort((left, right) => left.cartId - right.cartId)
}

export function upsertCart(current: LiveCart[], update: CartUpdate): LiveCart[] {
  return mergeLiveCarts(current, [cartFromUpdate(update)])
}

export function alertFromMessage(message: MarshalMessage): PaceAlert | null {
  const text = message.message.trim()
  if (text === '') return null

  return {
    id: null,
    courseId: message.course_id,
    cartId: message.cart_id ?? null,
    paceStatus: asPace(message.pace_status),
    message: text,
  }
}

function sameAlert(left: PaceAlert, right: PaceAlert): boolean {
  return left.cartId === right.cartId
    && left.message === right.message
    && left.paceStatus === right.paceStatus
}

export function pushAlert(current: PaceAlert[], incoming: PaceAlert): PaceAlert[] {
  if (current.some((alert) => sameAlert(alert, incoming) || (incoming.id !== null && alert.id === incoming.id))) {
    return current
  }

  return [incoming, ...current].slice(0, 50)
}

export function mergeAlerts(current: PaceAlert[], fetched: PaceAlert[]): PaceAlert[] {
  const fetchedIds = new Set(fetched.map((alert) => alert.id).filter((id) => id !== null))
  const pending = current.filter((alert) => alert.id === null && !fetched.some((item) => sameAlert(item, alert)))
  const saved = fetched.filter((alert) => alert.id === null || fetchedIds.has(alert.id))

  return [...pending, ...saved].slice(0, 50)
}
