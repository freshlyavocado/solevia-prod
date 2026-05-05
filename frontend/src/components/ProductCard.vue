<script setup lang="ts">
import type { Product } from '../types'
import { Heart } from 'lucide-vue-next'

defineProps<{
  product: Product
}>()

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
    <button class="absolute top-2 right-2 z-10 p-2 text-gray-400 hover:text-red-500 transition-colors">
      <Heart class="h-5 w-5" />
    </button>

    <!-- Product Image -->
    <RouterLink :to="`/product/${product.slug}`" class="block aspect-[4/3] overflow-hidden bg-gray-50 flex items-center justify-center p-4">
      <img 
        :src="product.images && product.images.length > 0 ? `http://localhost:8000/storage/${product.images[0].image_url}` : 'https://placehold.co/400x300?text=No+Image'" 
        :alt="product.name" 
        class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105 mix-blend-multiply"
      />
    </RouterLink>

    <!-- Product Details -->
    <div class="p-4 flex flex-col flex-1">
      <span class="text-base font-bold text-[#3771C8] uppercase tracking-wide mb-1">{{ product.brand?.name || 'Brand' }}</span>
      <RouterLink :to="`/product/${product.slug}`" class="text-base font-bold text-gray-900 mb-2 line-clamp-2 hover:text-[#3771C8] transition-colors">
        {{ product.name }}
      </RouterLink>
      <div class="mt-auto pt-2">
        <div v-if="product.discount_price" class="flex items-center gap-2">
          <span class="text-xl font-extrabold text-gray-900">{{ formatPrice(product.discount_price) }}</span>
          <span class="text-sm text-gray-500 line-through">{{ formatPrice(product.price) }}</span>
        </div>
        <div v-else class="text-xl font-extrabold text-gray-900">
          {{ formatPrice(product.price) }}
        </div>
      </div>
    </div>
  </div>
</template>
