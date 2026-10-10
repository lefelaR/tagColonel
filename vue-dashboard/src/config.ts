function trimmed(value: unknown, fallback: string): string {
  if (typeof value !== 'string' || value.trim() === '') return fallback
  return value.replace(/\/$/, '')
}

export function laravelApiUrl(): string {
  return trimmed(import.meta.env.VITE_LARAVEL_API, 'http://localhost:8000')
}

export function socketUrl(): string {
  return trimmed(import.meta.env.VITE_SOCKET_URL, 'http://localhost:3001')
}

export function courseId(): number {
  const parsed = Number(import.meta.env.VITE_COURSE_ID ?? 1)
  return Number.isInteger(parsed) && parsed > 0 ? parsed : 1
}
