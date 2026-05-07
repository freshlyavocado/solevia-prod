<!-- 
  INFO FILE
  Nama: ProfileView.vue
  Fungsi: Menampilkan detail profil pengguna dan riwayat pesanan (Order History).
-->
<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import api from '../services/api'
import { RouterLink, useRouter } from 'vue-router'

const authStore = useAuthStore() // Akses data user yang login
const router = useRouter() // Untuk mengalihkan halaman
const orders = ref<any[]>([]) // Menyimpan daftar pesanan dari backend
const loadingOrders = ref(true) // Status loading saat mengambil pesanan

// Dijalankan otomatis saat halaman dibuka
onMounted(async () => {
  // Jika user belum login, paksa pindah ke halaman login
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return
  }
  try {
    // Memanggil API pesanan (hanya mengembalikan pesanan milik user ini)
    const { data } = await api.get('/orders')
    orders.value = data.data || data // Simpan ke state
  } catch (error) {
    console.error('Failed to load orders', error)
  } finally {
    loadingOrders.value = false // Matikan loading
  }
})

// Fungsi mengubah angka ke format mata uang Rupiah
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price)
}

// Fungsi mengubah string format tanggal dari database menjadi format yang mudah dibaca
const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(date)
}
</script>

<template>
  <div class="container mx-auto px-4 py-12 min-h-[70vh]">
    <div class="flex flex-col md:flex-row gap-8 max-w-6xl mx-auto">
      
      <!-- Profile Sidebar -->
      <div class="w-full md:w-1/3">
        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm sticky top-24">
          <div class="flex flex-col items-center text-center">
            <div class="w-24 h-24 bg-[#3771C8] text-white rounded-full flex items-center justify-center text-3xl font-bold mb-4 shadow-md">
              {{ authStore.user?.name?.charAt(0).toUpperCase() || 'U' }}
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ authStore.user?.name || 'User' }}</h2>
            <p class="text-gray-500 mb-6">{{ authStore.user?.email || 'user@example.com' }}</p>
            
            <div class="w-full border-t border-gray-100 pt-6">
              <button @click="authStore.logout(); $router.push('/login')" class="w-full py-3 border border-red-500 text-red-500 font-bold rounded-xl hover:bg-red-50 transition">
                Sign Out
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="w-full md:w-2/3">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8 uppercase tracking-tight">Order History</h1>
        
        <div v-if="loadingOrders" class="flex justify-center py-10">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#3771C8]"></div>
        </div>
        
        <div v-else-if="orders.length === 0" class="bg-gray-50 border border-gray-200 rounded-2xl p-10 text-center">
          <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">No orders yet</h3>
          <p class="text-gray-500 mb-6">Looks like you haven't made any purchases.</p>
          <RouterLink to="/" class="px-6 py-3 bg-[#3771C8] text-white font-bold rounded-xl hover:opacity-90 transition inline-block">
            Start Shopping
          </RouterLink>
        </div>

        <div v-else class="space-y-6">
          <div v-for="order in orders" :key="order.id" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <div class="flex flex-wrap justify-between items-center border-b border-gray-100 pb-4 mb-4 gap-4">
              <div>
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Order #{{ order.order_number }}</p>
                <p class="text-gray-900 font-medium">{{ formatDate(order.created_at) }}</p>
              </div>
              <div class="text-right">
                <p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Total Amount</p>
                <p class="text-[#3771C8] font-bold text-lg">{{ formatPrice(order.total_amount) }}</p>
              </div>
              <div>
                <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider"
                  :class="{
                    'bg-yellow-100 text-yellow-700': order.status === 'pending',
                    'bg-blue-100 text-blue-700': order.status === 'processing',
                    'bg-green-100 text-green-700': order.status === 'completed',
                    'bg-red-100 text-red-700': order.status === 'cancelled'
                  }"
                >
                  {{ order.status }}
                </span>
              </div>
            </div>
            
            <div class="flex justify-end mt-4">
              <button class="text-sm font-bold text-[#3771C8] hover:underline">
                View Details
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>
