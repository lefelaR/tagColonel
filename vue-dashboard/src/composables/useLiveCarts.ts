import { onMounted, onUnmounted } from 'vue'
import { laravelApiUrl, socketUrl } from '../config'
import { mergeLiveCarts, upsertCart } from '../domain/mergeCarts'
import { createCartService } from '../services/CartService'
import { courseSocket } from '../services/courseSocket'
import { useCartStore } from '../stores/cartStore'
import type { CartUpdate } from '../types/dashboard'

export function useLiveCarts(courseId: number) {
  const store = useCartStore()
  const carts = createCartService(laravelApiUrl())
  const socket = courseSocket(socketUrl())

  function onConnect() {
    store.connected = true
    socket.emit('join:course', courseId)
  }

  function onDisconnect() {
    store.connected = false
  }

  function onUpdate(payload: CartUpdate) {
    if (payload.course_id !== courseId) return
    store.carts = upsertCart(store.carts, payload)
  }

  onMounted(async () => {
    socket.on('connect', onConnect)
    socket.on('disconnect', onDisconnect)
    socket.on('cart:update', onUpdate)
    if (socket.connected) onConnect()

    try {
      store.carts = mergeLiveCarts(store.carts, await carts.listLive(courseId))
    } catch (error) {
      console.error('Live cart load failed', error)
    }
  })

  onUnmounted(() => {
    socket.off('connect', onConnect)
    socket.off('disconnect', onDisconnect)
    socket.off('cart:update', onUpdate)
  })
}
