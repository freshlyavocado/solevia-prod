<!-- 
  INFO FILE
  Nama: ProductCard.vue
  Fungsi: Komponen reusable (bisa dipakai ulang) untuk menampilkan kotak produk (gambar, nama, harga) di berbagai halaman.
-->

<script setup lang="ts">
import type { Product } from '../types'
import { Heart } from 'lucide-vue-next'
import { useAuthStore } from '../stores/auth'
import api, { storageUrl } from '../services/api'

const props = defineProps<{
  product: Product
  hideWishlistButton?: boolean
}>()

const authStore = useAuthStore()

const addToWishlist = async (e: Event) => {
  e.preventDefault()
  if (!authStore.isAuthenticated) {
    alert('Please log in to add items to your wishlist.')
    return
  }
  try {
    await api.post('/wishlists', { product_id: props.product.id })
    alert('Added to wishlist successfully!')
  } catch (error) {
    console.error(error)
    alert('Failed to add to wishlist.')
  }
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price)
}
</script>

<template>
  <div class="group relative flex flex-col bg-white overflow-hidden transition-transform duration-300 hover:-translate-y-1">
    <!-- Wishlist Button -->
    <button v-if="!hideWishlistButton" @click.prevent="addToWishlist" class="absolute top-2 right-2 z-10 p-2 text-gray-400 hover:text-red-500 transition-colors">
      <Heart class="h-5 w-5" />
    </button>

    <!-- Product Image -->
    <RouterLink :to="`/product/${product.slug}`" class="block aspect-[4/3] overflow-hidden bg-gray-50 flex items-center justify-center p-4">
      <img 
        :src="product.images && product.images.length > 0 ? storageUrl(product.images[0]?.image_url) : 'https://placehold.co/400x300?text=No+Image'" 
        :alt="product.name" 
        class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105 mix-blend-multiply"
      />
    </RouterLink>

    <!-- Product Details -->
    <div class="p-4 flex flex-col flex-1">
      <span class="text-base font-semibold text-[#3771C8] uppercase tracking-wide mb-1">{{ product.brand?.name || 'Brand' }}</span>
      <RouterLink :to="`/product/${product.slug}`" class="text-base font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-[#3771C8] transition-colors">
        {{ product.name }}
      </RouterLink>
      <div class="mt-auto pt-2">
        <div v-if="product.discount_price" class="flex items-center gap-2">
          <span class="text-xl font-bold text-gray-900">{{ formatPrice(product.discount_price) }}</span>
          <span class="text-sm text-gray-500 line-through">{{ formatPrice(product.price) }}</span>
        </div>
        <div v-else class="text-xl font-bold text-gray-900">
          {{ formatPrice(product.price) }}
        </div>
      </div>
    </div>
  </div>
</template>
