import type { PaceStatus } from '../types/dashboard'

export function paceLabel(status: PaceStatus | null): string {
  if (status === 'on_pace') return 'On pace'
  if (status === 'slow') return 'Slow'
  if (status === 'very_slow') return 'Very slow'
  return 'No pace'
}

export function paceBadgeClass(status: PaceStatus | null): string {
  if (status === 'on_pace') return 'bg-emerald-500/15 text-emerald-300'
  if (status === 'slow') return 'bg-amber-500/15 text-amber-300'
  if (status === 'very_slow') return 'bg-red-500/15 text-red-300'
  return 'bg-slate-500/15 text-slate-300'
}

export function paceMarkerColor(status: PaceStatus | null): string {
  if (status === 'on_pace') return '#34d399'
  if (status === 'slow') return '#fbbf24'
  if (status === 'very_slow') return '#f87171'
  return '#94a3b8'
}
