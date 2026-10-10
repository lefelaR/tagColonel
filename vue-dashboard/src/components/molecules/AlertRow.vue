<script setup lang="ts">
import PaceBadge from '../atoms/PaceBadge.vue'
import type { PaceStatus } from '../../types/dashboard'

defineProps<{
  message: string
  cartId: number | null
  status: PaceStatus | null
}>()
</script>

<template>
  <li class="border-b border-slate-800 py-2 last:border-b-0">
    <div class="mb-1 flex items-center justify-between gap-2">
      <p class="text-xs text-slate-400">{{ cartId === null ? 'Marshal' : `Cart ${cartId}` }}</p>
      <PaceBadge :status="status" />
    </div>
    <p class="text-sm text-slate-100">{{ message }}</p>
  </li>
</template>
