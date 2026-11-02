<script setup lang="ts">
import ConnectionStatus from '../atoms/ConnectionStatus.vue'
import CourseSelect from '../molecules/CourseSelect.vue'
import AlertPanel from '../organisms/AlertPanel.vue'
import MapView from '../organisms/MapView.vue'
import PaceDashboard from '../organisms/PaceDashboard.vue'
import type { GolfCourse, LiveCart, PaceAlert } from '../../types/dashboard'

defineProps<{
  courses: GolfCourse[]
  courseId: number
  latitude: number
  longitude: number
  carts: LiveCart[]
  alerts: PaceAlert[]
  connected: boolean
}>()

defineEmits<{ 'update:courseId': [number] }>()
</script>

<template>
  <div class="flex h-screen flex-col bg-slate-950 text-slate-100">
    <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 px-4 py-3">
      <h1 class="text-lg font-semibold">FairwayFlow</h1>
      <CourseSelect
        :courses="courses"
        :model-value="courseId"
        @update:model-value="$emit('update:courseId', $event)"
      />
      <ConnectionStatus :connected="connected" />
    </header>
    <div class="grid min-h-0 flex-1 grid-cols-1 lg:grid-cols-3">
      <div class="min-h-80 lg:col-span-2 lg:min-h-0">
        <MapView :carts="carts" :latitude="latitude" :longitude="longitude" />
      </div>
      <aside class="flex min-h-0 flex-col gap-4 overflow-hidden border-t border-slate-800 p-4 lg:border-t-0 lg:border-l">
        <PaceDashboard class="min-h-0 flex-1 overflow-hidden" :carts="carts" />
        <AlertPanel class="min-h-0 flex-1 overflow-hidden" :alerts="alerts" />
      </aside>
    </div>
  </div>
</template>
