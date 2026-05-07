<!-- 
  INFO FILE
  Nama: CartView.vue
  Fungsi: Menampilkan daftar produk yang ada di keranjang belanja, ringkasan harga, dan opsi checkout.
-->

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useCartStore } from '../stores/cart'
import { useAuthStore } from '../stores/auth'
import { Trash2, Plus, Minus, ArrowRight } from 'lucide-vue-next'
import { RouterLink } from 'vue-router'

// --- LOGIKA KERANJANG BELANJA ---

// Mengimpor stores untuk memanipulasi data keranjang dan mengecek status login
const cartStore = useCartStore()
const authStore = useAuthStore()

// Saat komponen pertama kali dimuat di layar
onMounted(() => {
  // Hanya ambil data keranjang jika pengguna sudah berhasil login
  if (authStore.isAuthenticated) {
    cartStore.fetchCart()
  }
})

// Fungsi bantuan untuk mengubah angka biasa menjadi format Rupiah (Rp)
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price)
}

// Menghitung subtotal dari semua item di keranjang secara otomatis dan reaktif
const subtotal = computed(() => {
  if (!cartStore.cart?.items) return 0
  return cartStore.cart.items.reduce((total, item) => {
    // Memastikan data produk dan harga tersedia (ambil harga diskon jika ada, jika tidak harga normal)
    const price = item.variant?.product?.discount_price || item.variant?.product?.price || 0
    // Mengalikan harga dengan jumlah kuantitas masing-masing barang
    return total + (price * item.quantity)
  }, 0)
})

// Total bayar saat ini diset sama dengan subtotal (tidak ada pajak tambahan)
const total = computed(() => subtotal.value)

// Fungsi untuk menambah atau mengurangi jumlah barang spesifik di keranjang
const updateQuantity = async (itemId: number, currentQty: number, change: number) => {
  const newQty = currentQty + change
  if (newQty < 1) return // Tidak bisa kurang dari 1
  await cartStore.updateItem(itemId, newQty) // Memperbarui ke backend via store
}

// Fungsi untuk menghapus satu produk dari keranjang
const removeItem = async (itemId: number) => {
  if (confirm('Are you sure you want to remove this item?')) {
    await cartStore.removeItem(itemId)
  }
}
</script>

<template>
  <div class="container mx-auto px-4 py-8 pb-24 min-h-[70vh]">
    <!-- Breadcrumb -->
    <div class="text-xs font-bold text-gray-800 tracking-wider mb-8 uppercase">
      <RouterLink to="/" class="hover:text-[#3771C8] transition">HOME</RouterLink> / <span class="text-[#3771C8]">SHOPPING CART</span>
    </div>

    <h1 class="text-3xl font-extrabold text-gray-900 mb-10 uppercase tracking-tight">Your Cart</h1>

    <div v-if="cartStore.loading && !cartStore.cart" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#3771C8]"></div>
    </div>

    <div v-else-if="!authStore.isAuthenticated" class="text-center py-20 bg-gray-50 rounded-2xl border border-gray-200">
      <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
      </div>
      <h2 class="text-2xl font-bold text-gray-900 mb-3">Please sign in</h2>
      <p class="text-gray-500 mb-8">You need to log in to view your shopping cart.</p>
      <RouterLink to="/login" class="inline-block px-8 py-3 bg-[#3771C8] text-white font-bold rounded-xl hover:opacity-90 transition shadow-sm">
        Log In
      </RouterLink>
    </div>

    <div v-else-if="cartStore.cart?.items && cartStore.cart.items.length > 0" class="flex flex-col lg:flex-row gap-12">
      
      <!-- Cart Items List -->
      <div class="flex-1 space-y-6">
        <div v-for="item in cartStore.cart.items" :key="item.id" class="flex flex-col sm:flex-row bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow relative">
          
          <!-- Image -->
          <div class="w-full sm:w-32 h-32 bg-gray-50 rounded-xl flex-shrink-0 flex items-center justify-center p-2 mb-4 sm:mb-0 sm:mr-6">
            <img 
              :src="item.variant?.product?.images?.[0]?.image_url ? `http://localhost:8000/storage/${item.variant.product.images[0].image_url}` : 'https://placehold.co/400x400?text=No+Image'" 
              :alt="item.variant?.product?.name" 
              class="w-full h-full object-contain mix-blend-multiply"
            />
          </div>

          <!-- Info -->
          <div class="flex-1 flex flex-col justify-between">
            <div>
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="font-bold text-lg text-gray-900 mb-1 leading-tight">
                    {{ item.variant?.product?.name || 'Unknown Product' }}
                  </h3>
                  <p class="text-sm text-gray-500 mb-2">
                    Size: <span class="font-semibold text-gray-700">{{ item.variant?.size || 'Default' }}</span>
                  </p>
                </div>
                <div class="text-right">
                  <p class="font-bold text-lg text-gray-900">
                    {{ formatPrice(item.variant?.product?.discount_price || item.variant?.product?.price || 0) }}
                  </p>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-between mt-4">
              <!-- Quantity Controls -->
              <div class="flex items-center border border-gray-300 rounded-full bg-white px-2 py-1">
                <button @click="updateQuantity(item.id, item.quantity, -1)" class="p-1 text-gray-500 hover:text-[#3771C8] transition" :disabled="cartStore.loading">
                  <Minus class="w-4 h-4" />
                </button>
                <span class="w-8 text-center font-bold text-sm">{{ item.quantity }}</span>
                <button @click="updateQuantity(item.id, item.quantity, 1)" class="p-1 text-gray-500 hover:text-[#3771C8] transition" :disabled="cartStore.loading">
                  <Plus class="w-4 h-4" />
                </button>
              </div>

              <!-- Remove Button -->
              <button @click="removeItem(item.id)" class="text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition" :disabled="cartStore.loading" title="Remove Item">
                <Trash2 class="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Summary Sidebar -->
      <div class="w-full lg:w-96 flex-shrink-0">
        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-8 sticky top-24">
          <h2 class="text-xl font-extrabold text-gray-900 mb-6 uppercase tracking-wider border-b pb-4">Order Summary</h2>
          
          <div class="space-y-4 mb-6">
            <div class="flex justify-between text-gray-600 font-medium">
              <span>Subtotal</span>
              <span>{{ formatPrice(subtotal) }}</span>
            </div>

            <div class="flex justify-between text-gray-600 font-medium">
              <span>Shipping</span>
              <span class="text-green-600 font-bold">Free</span>
            </div>
          </div>

          <div class="border-t border-gray-200 pt-6 mb-8">
            <div class="flex justify-between items-center">
              <span class="text-lg font-bold text-gray-900">Total</span>
              <span class="text-2xl font-extrabold text-[#3771C8]">{{ formatPrice(total) }}</span>
            </div>
          </div>

          <button class="w-full py-4 bg-[#3771C8] text-white font-bold rounded-xl hover:bg-blue-700 hover:shadow-lg transition flex justify-center items-center group">
            Proceed to Checkout
            <ArrowRight class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" />
          </button>
          
          <div class="mt-4 text-center">
            <RouterLink to="/" class="text-sm font-bold text-gray-500 hover:text-[#3771C8] transition underline">
              Continue Shopping
            </RouterLink>
          </div>
        </div>
      </div>

    </div>

    <!-- Empty Cart -->
    <div v-else class="text-center py-20 bg-gray-50 rounded-2xl border border-gray-200">
      <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-gray-900 mb-3">Your cart is empty</h2>
      <p class="text-gray-500 mb-8 max-w-md mx-auto">Looks like you haven't added anything to your cart yet. Discover our latest collection and find something you love!</p>
      <RouterLink to="/" class="inline-flex px-8 py-3 bg-[#3771C8] text-white font-bold rounded-xl hover:opacity-90 transition">
        Start Shopping
      </RouterLink>
    </div>

  </div>
</template>
