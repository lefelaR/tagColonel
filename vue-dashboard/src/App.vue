<script setup>
// Presentation only. Hosts come from Vite env, so this component stays free of infrastructure details.
const laravelApi = import.meta.env.VITE_LARAVEL_API ?? '';
const socketUrl = import.meta.env.VITE_SOCKET_URL ?? '';
</script>

<template>
  <main>
    <h1>FairwayFlow</h1>
    <p>Dashboard skeleton is up.</p>
    <p>Laravel API: {{ laravelApi }}</p>
    <p>Socket: {{ socketUrl }}</p>
  </main>
</template>
