<script setup lang="ts">
import { computed, onMounted } from 'vue'
import { useProductStore } from '../stores/product'
import ProductCard from '../components/ProductCard.vue'

const productStore = useProductStore()

onMounted(() => {
  if (productStore.products.length === 0) {
    productStore.fetchProducts()
  }
})

// Filter products that have a discount
const dealsProducts = computed(() => {
  return productStore.products.filter(p => p.discount_price !== null && p.discount_price !== undefined)
})
</script>

<template>
  <div class="container mx-auto px-4 py-8 pb-24">
    <!-- Breadcrumb -->
    <div class="text-xs font-bold text-gray-900 uppercase tracking-widest mb-6">
      <RouterLink to="/" class="hover:text-[#3771C8] transition">HOME</RouterLink> / 
      <span class="text-[#3771C8]">DEALS</span>
    </div>

    <!-- Header -->
    <div class="flex items-end justify-between mb-8 border-b border-gray-200 pb-4">
      <div>
        <h1 class="text-3xl font-extrabold text-gray-900">Today's Deals</h1>
        <p class="text-sm text-gray-500 mt-2">Grab your favorite gear at discounted prices.</p>
      </div>
      <div class="text-sm font-bold text-gray-500">
        {{ dealsProducts.length }} Products
      </div>
    </div>

    <div v-if="productStore.loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#3771C8]"></div>
    </div>

    <div v-else-if="dealsProducts.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
      <ProductCard v-for="product in dealsProducts" :key="product.id" :product="product" />
    </div>

    <div v-else class="text-center py-20 bg-gray-50 rounded-2xl">
      <p class="text-gray-500 font-medium mb-4">No deals are currently available.</p>
      <RouterLink to="/" class="inline-block px-6 py-3 bg-[#3771C8] text-white font-bold rounded-xl hover:opacity-90 transition">
        Continue Shopping
      </RouterLink>
    </div>
  </div>
</template>
