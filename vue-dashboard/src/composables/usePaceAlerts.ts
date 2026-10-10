import { onMounted, onUnmounted } from 'vue'
import { laravelApiUrl, socketUrl } from '../config'
import { alertFromMessage, mergeAlerts, pushAlert } from '../domain/mergeCarts'
import { createAlertService } from '../services/AlertService'
import { courseSocket } from '../services/courseSocket'
import { useCartStore } from '../stores/cartStore'
import type { MarshalMessage } from '../types/dashboard'

export function usePaceAlerts(courseId: number) {
  const store = useCartStore()
  const alerts = createAlertService(laravelApiUrl())
  const socket = courseSocket(socketUrl())

  function onMessage(payload: MarshalMessage) {
    if (payload.course_id !== courseId) return
    const alert = alertFromMessage(payload)
    if (alert === null) return
    store.alerts = pushAlert(store.alerts, alert)
  }

  onMounted(async () => {
    socket.on('marshal:message', onMessage)

    try {
      store.alerts = mergeAlerts(store.alerts, await alerts.list(courseId))
    } catch (error) {
      console.error('Pace alert load failed', error)
    }
  })

  onUnmounted(() => {
    socket.off('marshal:message', onMessage)
  })
}
