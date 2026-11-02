import { onMounted, onUnmounted, toValue, watch, type MaybeRef } from 'vue'
import { laravelApiUrl, socketUrl } from '../config'
import { mergeLiveCarts, upsertCart } from '../domain/mergeCarts'
import { createCartService } from '../services/CartService'
import { courseSocket } from '../services/courseSocket'
import { useCartStore } from '../stores/cartStore'
import type { CartUpdate } from '../types/dashboard'

export function useLiveCarts(courseId: MaybeRef<number>) {
  const store = useCartStore()
  const carts = createCartService(laravelApiUrl())
  const socket = courseSocket(socketUrl())

  function onConnect() {
    store.connected = true
    socket.emit('join:course', toValue(courseId))
  }

  function onDisconnect() {
    store.connected = false
  }

  function onUpdate(payload: CartUpdate) {
    if (payload.course_id !== toValue(courseId)) return
    store.carts = upsertCart(store.carts, payload)
  }

  async function load(id: number) {
    store.carts = []
    socket.emit('join:course', id)

    try {
      const live = await carts.listLive(id)
      if (toValue(courseId) !== id) return
      store.carts = mergeLiveCarts(store.carts, live)
    } catch (error) {
      console.error('Live cart load failed', error)
    }
  }

  onMounted(() => {
    socket.on('connect', onConnect)
    socket.on('disconnect', onDisconnect)
    socket.on('cart:update', onUpdate)
    if (socket.connected) onConnect()
  })

  watch(() => toValue(courseId), (id) => {
    void load(id)
  }, { immediate: true })

  onUnmounted(() => {
    socket.off('connect', onConnect)
    socket.off('disconnect', onDisconnect)
    socket.off('cart:update', onUpdate)
  })
}
