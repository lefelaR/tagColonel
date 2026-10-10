<script setup lang="ts">
import { storeToRefs } from 'pinia'
import { computed, onMounted, ref } from 'vue'
import { useLiveCarts } from '../composables/useLiveCarts'
import { usePaceAlerts } from '../composables/usePaceAlerts'
import { courseId, laravelApiUrl } from '../config'
import DashboardTemplate from '../components/templates/DashboardTemplate.vue'
import { createCourseService } from '../services/CourseService'
import { useCartStore } from '../stores/cartStore'
import type { GolfCourse } from '../types/dashboard'

const courses = ref<GolfCourse[]>([])
const selectedCourseId = ref(courseId())

const selectedCourse = computed(() => courses.value.find((course) => course.id === selectedCourseId.value))
const latitude = computed(() => selectedCourse.value?.latitude ?? -33.956)
const longitude = computed(() => selectedCourse.value?.longitude ?? 18.4943)

useLiveCarts(selectedCourseId)
usePaceAlerts(selectedCourseId)

const { carts, alerts, connected } = storeToRefs(useCartStore())

onMounted(async () => {
  try {
    courses.value = await createCourseService(laravelApiUrl()).list()
  } catch (error) {
    console.error('Course list failed', error)
  }

  if (courses.value.length > 0 && !courses.value.some((course) => course.id === selectedCourseId.value)) {
    selectedCourseId.value = courses.value[0].id
  }
})
</script>

<template>
  <DashboardTemplate
    v-model:course-id="selectedCourseId"
    :courses="courses"
    :latitude="latitude"
    :longitude="longitude"
    :carts="carts"
    :alerts="alerts"
    :connected="connected"
  />
</template>
