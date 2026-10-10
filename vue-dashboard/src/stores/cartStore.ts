import { defineStore } from 'pinia'
import type { LiveCart, PaceAlert } from '../types/dashboard'

// Pinia holds state only. Composables decide how carts and alerts change.
export const useCartStore = defineStore('cart', {
  state: () => ({
    carts: [] as LiveCart[],
    alerts: [] as PaceAlert[],
    connected: false,
  }),
})
