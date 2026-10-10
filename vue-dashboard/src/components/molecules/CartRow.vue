<script setup lang="ts">
import PaceBadge from '../atoms/PaceBadge.vue'
import type { PaceStatus } from '../../types/dashboard'

defineProps<{
  cartId: number
  speed: number
  hole: number | null
  status: PaceStatus | null
}>()
</script>

<template>
  <li class="flex items-center justify-between gap-3 border-b border-slate-800 py-2 last:border-b-0">
    <div class="min-w-0">
      <p class="text-sm font-medium text-slate-100">Cart {{ cartId }}</p>
      <p class="text-xs text-slate-400">
        <span v-if="hole !== null">Hole {{ hole }} · </span>{{ speed.toFixed(1) }} m/s
      </p>
    </div>
    <PaceBadge :status="status" />
  </li>
</template>
