import { createPinia } from 'pinia'
import { createApp } from 'vue'
import DashboardPage from './pages/DashboardPage.vue'
import 'leaflet/dist/leaflet.css'
import './styles/main.css'

createApp(DashboardPage).use(createPinia()).mount('#app')
