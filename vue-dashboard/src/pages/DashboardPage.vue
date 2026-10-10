<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { useLiveCarts } from '../composables/useLiveCarts'
import { usePaceAlerts } from '../composables/usePaceAlerts'
import { courseId } from '../config'
import DashboardTemplate from '../components/templates/DashboardTemplate.vue'
import { useCartStore } from '../stores/cartStore'

const selectedCourse = courseId()
useLiveCarts(selectedCourse)
usePaceAlerts(selectedCourse)

const { carts, alerts, connected } = storeToRefs(useCartStore())
</script>

<template>
  <DashboardTemplate :course-id="selectedCourse" :carts="carts" :alerts="alerts" :connected="connected" />
</template>
