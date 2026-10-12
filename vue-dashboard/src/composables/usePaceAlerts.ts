import { onMounted, onUnmounted, toValue, watch, type MaybeRef } from 'vue'
import { laravelApiUrl, socketUrl } from '../config'
import { alertFromMessage, mergeAlerts, pushAlert } from '../domain/mergeCarts'
import { createAlertService } from '../services/AlertService'
import { courseSocket } from '../services/courseSocket'
import { useCartStore } from '../stores/cartStore'
import type { MarshalMessage } from '../types/dashboard'

export function usePaceAlerts(courseId: MaybeRef<number>) {
  const store = useCartStore()
  const alerts = createAlertService(laravelApiUrl())
  const socket = courseSocket(socketUrl())

  function onMessage(payload: MarshalMessage) {
    if (payload.course_id !== toValue(courseId)) return
    const alert = alertFromMessage(payload)
    if (alert === null) return
    store.alerts = pushAlert(store.alerts, alert)
  }

  async function load(id: number) {
    store.alerts = []

    try {
      const listed = await alerts.list(id)
      if (toValue(courseId) !== id) return
      store.alerts = mergeAlerts(store.alerts, listed)
    } catch (error) {
      console.error('Pace alert load failed', error)
    }
  }

  onMounted(() => {
    socket.on('marshal:message', onMessage)
  })

  watch(() => toValue(courseId), (id) => {
    void load(id)
  }, { immediate: true })

  onUnmounted(() => {
    socket.off('marshal:message', onMessage)
  })
}
