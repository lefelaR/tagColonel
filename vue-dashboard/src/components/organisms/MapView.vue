<script setup lang="ts">
import L from 'leaflet'
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { paceMarkerColor } from '../../styles/pace'
import type { LiveCart } from '../../types/dashboard'

const props = defineProps<{ carts: LiveCart[] }>()

const mapEl = ref<HTMLElement | null>(null)
let map: L.Map | null = null
const markers = new Map<number, L.CircleMarker>()

function syncMarkers(carts: LiveCart[]) {
  if (!map) return

  const seen = new Set<number>()

  for (const cart of carts) {
    seen.add(cart.cartId)
    const latLng: L.LatLngExpression = [cart.latitude, cart.longitude]
    const color = paceMarkerColor(cart.paceStatus)
    const existing = markers.get(cart.cartId)

    if (existing) {
      existing.setLatLng(latLng)
      existing.setStyle({ color, fillColor: color })
      continue
    }

    const marker = L.circleMarker(latLng, {
      radius: 7,
      color,
      fillColor: color,
      fillOpacity: 0.9,
      weight: 2,
    }).addTo(map)
    marker.bindTooltip(`Cart ${cart.cartId}`)
    markers.set(cart.cartId, marker)
  }

  for (const [id, marker] of markers) {
    if (seen.has(id)) continue
    marker.remove()
    markers.delete(id)
  }
}

function onResize() {
  map?.invalidateSize()
}

onMounted(() => {
  if (!mapEl.value) return

  map = L.map(mapEl.value, { zoomControl: true }).setView([-33.9249, 18.4241], 16)
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap',
    maxZoom: 19,
  }).addTo(map)
  syncMarkers(props.carts)
  window.addEventListener('resize', onResize)
  map.invalidateSize()
})

watch(() => props.carts, (carts) => syncMarkers(carts), { deep: true })

onBeforeUnmount(() => {
  window.removeEventListener('resize', onResize)
  map?.remove()
  map = null
  markers.clear()
})
</script>

<template>
  <div ref="mapEl" class="h-full min-h-80 w-full"></div>
</template>
