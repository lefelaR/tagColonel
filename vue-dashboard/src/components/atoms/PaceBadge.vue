<script setup lang="ts">
import { paceBadgeClass, paceLabel } from '../../styles/pace'
import type { PaceStatus } from '../../types/dashboard'

defineProps<{ status: PaceStatus | null }>()
</script>

<template>
  <span class="rounded-full px-2 py-0.5 text-xs font-medium" :class="paceBadgeClass(status)">
    {{ paceLabel(status) }}
  </span>
</template>
