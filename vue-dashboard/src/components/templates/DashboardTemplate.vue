<script setup lang="ts">
import ConnectionStatus from '../atoms/ConnectionStatus.vue'
import AlertPanel from '../organisms/AlertPanel.vue'
import MapView from '../organisms/MapView.vue'
import PaceDashboard from '../organisms/PaceDashboard.vue'
import type { LiveCart, PaceAlert } from '../../types/dashboard'

defineProps<{
  courseId: number
  carts: LiveCart[]
  alerts: PaceAlert[]
  connected: boolean
}>()
</script>

<template>
  <div class="flex h-screen flex-col bg-slate-950 text-slate-100">
    <header class="flex items-center justify-between border-b border-slate-800 px-4 py-3">
      <div>
        <h1 class="text-lg font-semibold">FairwayFlow</h1>
        <p class="text-xs text-slate-500">Course {{ courseId }}</p>
      </div>
      <ConnectionStatus :connected="connected" />
    </header>
    <div class="grid min-h-0 flex-1 grid-cols-1 lg:grid-cols-3">
      <div class="min-h-80 lg:col-span-2 lg:min-h-0">
        <MapView :carts="carts" />
      </div>
      <aside class="flex min-h-0 flex-col gap-4 overflow-hidden border-t border-slate-800 p-4 lg:border-t-0 lg:border-l">
        <PaceDashboard class="min-h-0 flex-1 overflow-hidden" :carts="carts" />
        <AlertPanel class="min-h-0 flex-1 overflow-hidden" :alerts="alerts" />
      </aside>
    </div>
  </div>
</template>
