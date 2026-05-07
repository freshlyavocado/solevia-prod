/**
 * INFO FILE
 * Nama: main.ts
 * Fungsi: File utama (Entry point) JavaScript yang menginisialisasi aplikasi Vue, Pinia, dan Router lalu me-mount-nya ke DOM.
 */

// Mengimpor file CSS utama yang berisi utilitas Tailwind CSS agar bisa merubah style di seluruh aplikasi
import './assets/main.css'

// Mengimpor fungsi createApp dari Vue untuk merakit aplikasi
import { createApp } from 'vue'
// Mengimpor createPinia untuk mengaktifkan sistem manajemen state (memori global)
import { createPinia } from 'pinia'

// Mengimpor komponen App.vue yang menjadi "bungkus luar" seluruh halaman
import App from './App.vue'
// Mengimpor konfigurasi rute/navigasi
import router from './router'

// Membuat instance aplikasi Vue
const app = createApp(App)

// Menambahkan plugin Pinia (Store) ke dalam aplikasi
app.use(createPinia())
// Menambahkan plugin Router (Navigasi SPA) ke dalam aplikasi
app.use(router)

// Menempelkan (mount) seluruh aplikasi ke dalam tag <div id="app"> di file index.html
app.mount('#app')
