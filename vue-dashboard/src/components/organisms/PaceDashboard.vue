<script setup lang="ts">
import CartRow from '../molecules/CartRow.vue'
import type { LiveCart } from '../../types/dashboard'

defineProps<{ carts: LiveCart[] }>()
</script>

<template>
  <section class="flex min-h-0 flex-col">
    <h2 class="text-sm font-semibold tracking-wide text-slate-300 uppercase">Pace</h2>
    <p v-if="carts.length === 0" class="mt-3 text-sm text-slate-400">Waiting for live carts.</p>
    <ul v-else class="mt-2 overflow-y-auto">
      <CartRow
        v-for="cart in carts"
        :key="cart.cartId"
        :cart-id="cart.cartId"
        :speed="cart.speed"
        :hole="cart.hole"
        :status="cart.paceStatus"
      />
    </ul>
  </section>
</template>
