import axios from 'axios'
import type { GolfCourse } from '../types/dashboard'

interface CourseRow {
  id: number
  name: string
  latitude: number
  longitude: number
}

export function createCourseService(baseUrl: string) {
  const http = axios.create({ baseURL: baseUrl, timeout: 5000 })

  return {
    async list(): Promise<GolfCourse[]> {
      const response = await http.get<{ data?: CourseRow[] }>('/api/courses')
      const rows = response.data?.data
      if (!Array.isArray(rows)) return []

      return rows.map((row) => ({
        id: row.id,
        name: row.name,
        latitude: row.latitude,
        longitude: row.longitude,
      }))
    },
  }
}
