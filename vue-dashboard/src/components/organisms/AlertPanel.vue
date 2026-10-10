<script setup lang="ts">
import AlertRow from '../molecules/AlertRow.vue'
import type { PaceAlert } from '../../types/dashboard'

defineProps<{ alerts: PaceAlert[] }>()
</script>

<template>
  <section class="flex min-h-0 flex-col border-t border-slate-800 pt-4">
    <h2 class="text-sm font-semibold tracking-wide text-slate-300 uppercase">Alerts</h2>
    <p v-if="alerts.length === 0" class="mt-3 text-sm text-slate-400">No pace alerts.</p>
    <ul v-else class="mt-2 overflow-y-auto">
      <AlertRow
        v-for="(alert, index) in alerts"
        :key="alert.id ?? `${alert.cartId}-${index}`"
        :message="alert.message"
        :cart-id="alert.cartId"
        :status="alert.paceStatus"
      />
    </ul>
  </section>
</template>
