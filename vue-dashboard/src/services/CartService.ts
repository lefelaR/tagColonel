import axios from 'axios'
import { asPace } from '../domain/mergeCarts'
import type { LiveCart } from '../types/dashboard'

interface LiveCartRow {
  cart_id: number
  latitude: number
  longitude: number
  speed: number
  hole?: number | null
  pace_status?: string | null
}

// API adapter. Components never call Laravel themselves.
export function createCartService(baseUrl: string) {
  const http = axios.create({ baseURL: baseUrl, timeout: 5000 })

  return {
    async listLive(courseId: number): Promise<LiveCart[]> {
      const response = await http.get<{ data?: LiveCartRow[] }>(`/api/courses/${courseId}/carts/live`)
      const rows = response.data?.data
      if (!Array.isArray(rows)) return []

      return rows.map((row) => ({
        cartId: row.cart_id,
        latitude: row.latitude,
        longitude: row.longitude,
        speed: row.speed,
        hole: row.hole ?? null,
        paceStatus: asPace(row.pace_status),
      }))
    },
  }
}
