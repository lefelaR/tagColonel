<script setup lang="ts">
import type { GolfCourse } from '../../types/dashboard'

defineProps<{
  courses: GolfCourse[]
  modelValue: number
}>()

const emit = defineEmits<{ 'update:modelValue': [number] }>()

function onChange(event: Event) {
  const value = Number((event.target as HTMLSelectElement).value)
  if (Number.isInteger(value) && value > 0) emit('update:modelValue', value)
}
</script>

<template>
  <label class="block text-xs text-slate-500">
    Course
    <select
      class="mt-1 block w-full min-w-56 rounded border border-slate-700 bg-slate-900 px-2 py-1 text-sm text-slate-100"
      :value="modelValue"
      :disabled="courses.length === 0"
      @change="onChange"
    >
      <option v-if="courses.length === 0" :value="modelValue">Loading courses</option>
      <option v-for="course in courses" :key="course.id" :value="course.id">
        {{ course.name }}
      </option>
    </select>
  </label>
</template>
