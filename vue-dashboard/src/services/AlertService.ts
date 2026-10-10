import axios from 'axios'
import { asPace } from '../domain/mergeCarts'
import type { PaceAlert } from '../types/dashboard'

interface AlertRow {
  id: number
  course_id: number
  cart_id: number | null
  pace_status: string | null
  message: string | null
}

export function createAlertService(baseUrl: string) {
  const http = axios.create({ baseURL: baseUrl, timeout: 5000 })

  return {
    async list(courseId: number): Promise<PaceAlert[]> {
      const response = await http.get<{ data?: AlertRow[] }>('/api/alerts', {
        params: { course_id: courseId },
      })
      const rows = response.data?.data
      if (!Array.isArray(rows)) return []

      return rows.map((row) => ({
        id: row.id,
        courseId: row.course_id,
        cartId: row.cart_id,
        paceStatus: asPace(row.pace_status),
        message: row.message ?? '',
      }))
    },
  }
}
