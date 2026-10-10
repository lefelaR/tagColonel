<script setup lang="ts">
defineProps<{ connected: boolean }>()
</script>

<template>
  <span class="text-sm" :class="connected ? 'text-emerald-300' : 'text-slate-400'">
    {{ connected ? 'Live' : 'Connecting' }}
  </span>
</template>
