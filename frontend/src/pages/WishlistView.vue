<!-- 
  INFO FILE
  Nama: WishlistView.vue
  Fungsi: Menampilkan daftar produk favorit yang disimpan pengguna (Wishlist).
-->

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import ProductCard from '../components/ProductCard.vue'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'

const wishlists = ref<any[]>([]) // Menyimpan daftar produk favorit
const loading = ref(true) // Status apakah data sedang diambil dari server
const authStore = useAuthStore() // Menggunakan authStore untuk mengecek apakah user sudah login

// Fungsi untuk mengambil data wishlist dari backend
const fetchWishlists = async () => {
  // Jika belum login, hentikan proses (karena butuh token)
  if (!authStore.isAuthenticated) {
    loading.value = false
    return
  }

  loading.value = true // Nyalakan animasi loading
  try {
    const { data } = await api.get('/wishlists')
    // Menyimpan data wishlist ke dalam state lokal
    wishlists.value = data.data || data
  } catch (error) {
    console.error("Error fetching wishlists", error)
  } finally {
    loading.value = false // Matikan animasi loading
  }
}

// Dijalankan otomatis saat halaman dibuka
onMounted(() => {
  fetchWishlists()
})

// Fungsi untuk menghapus produk dari daftar wishlist
const removeWishlist = async (id: number) => {
  try {
    // Memanggil API delete ke backend
    await api.delete(`/wishlists/${id}`)
    // Memperbarui tampilan secara instan (menghapus item dari array tanpa reload)
    wishlists.value = wishlists.value.filter(w => w.id !== id)
  } catch (error) {
    console.error("Error removing wishlist", error)
  }
}
</script>

<template>
  <div class="container mx-auto px-4 py-8 pb-24 min-h-[70vh]">
    <!-- Breadcrumb -->
    <div class="text-xs font-bold text-gray-800 tracking-wider mb-8 uppercase">
      <RouterLink to="/" class="hover:text-[#3771C8] transition">HOME</RouterLink> / <span class="text-[#3771C8]">WISHLIST</span>
    </div>

    <div class="mb-10">
      <h1 class="text-3xl font-extrabold text-gray-900 mb-2 uppercase tracking-tight">Your Wishlist</h1>
      <p class="text-gray-500 font-medium">
        {{ wishlists.length }} {{ wishlists.length === 1 ? 'item' : 'items' }} saved
      </p>
    </div>

    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#3771C8]"></div>
    </div>
    
    <div v-else-if="!authStore.isAuthenticated" class="text-center py-20 bg-gray-50 rounded-2xl border border-gray-200">
      <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
      </div>
      <h2 class="text-2xl font-bold text-gray-900 mb-3">Please sign in</h2>
      <p class="text-gray-500 mb-8">You need to log in to view and save items to your wishlist.</p>
      <RouterLink to="/login" class="inline-block px-8 py-3 bg-[#3771C8] text-white font-bold rounded-xl hover:opacity-90 transition shadow-sm">
        Log In
      </RouterLink>
    </div>

    <div v-else-if="wishlists.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div v-for="item in wishlists" :key="item.id" class="relative group">
        <!-- Render the ProductCard, assuming item.product holds the actual product object -->
        <ProductCard v-if="item.product" :product="item.product" :hideWishlistButton="true" />
        
        <!-- Delete overlay button -->
        <button 
          @click="removeWishlist(item.id)" 
          class="absolute top-4 right-4 z-20 bg-white/90 p-2 rounded-full text-red-500 shadow-md opacity-0 group-hover:opacity-100 transition hover:bg-red-50"
          title="Remove from wishlist"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
    </div>

    <div v-else class="text-center py-20 bg-gray-50 rounded-2xl border border-gray-200">
      <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-gray-900 mb-3">Your wishlist is empty</h2>
      <p class="text-gray-500 mb-8 max-w-md mx-auto">Save your favorite items here to keep track of them and buy them later.</p>
      <RouterLink to="/" class="inline-flex px-8 py-3 border-2 border-[#3771C8] text-[#3771C8] font-bold rounded-xl hover:bg-blue-50 transition">
        Browse Products
      </RouterLink>
    </div>
  </div>
</template>
