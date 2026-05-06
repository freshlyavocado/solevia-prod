<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useProductStore } from '../stores/product'
import ProductCard from '../components/ProductCard.vue'
import api from '../services/api'
import type { Product } from '../types'

const route = useRoute()
const productStore = useProductStore()
const products = ref<Product[]>([])
const loading = ref(true)

// Filters
const selectedBrand = ref<string>('')
const selectedCategory = ref<string>('')
const priceRange = ref<number>(5000000)

const fetchSearchResults = async () => {
  const query = route.query.q as string
  if (!query) {
    products.value = []
    loading.value = false
    return
  }

  loading.value = true
  try {
    const { data } = await api.get('/products', { params: { search: query, per_page: 100 } })
    products.value = data.data || data
    if (productStore.brands.length === 0) {
      await productStore.fetchBrands()
    }
    if (productStore.categories.length === 0) {
      await productStore.fetchCategories()
    }
  } catch (error) {
    console.error("Error fetching search results", error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchSearchResults()
})

watch(() => route.query.q, () => {
  // Reset filters when search query changes
  selectedBrand.value = ''
  selectedCategory.value = ''
  priceRange.value = 5000000
  fetchSearchResults()
})

const filteredProducts = computed(() => {
  return products.value.filter(p => {
    // Brand filter
    if (selectedBrand.value && p.brand) {
      if (p.brand.name !== selectedBrand.value) return false
    }
    // Category filter
    if (selectedCategory.value && p.category) {
      if (p.category.name !== selectedCategory.value) return false
    }
    // Price filter
    const effectivePrice = p.discount_price || p.price
    if (effectivePrice > priceRange.value) return false
    
    return true
  })
})

const clearFilters = () => {
  selectedBrand.value = ''
  selectedCategory.value = ''
  priceRange.value = 5000000
}
</script>

<template>
  <div class="container mx-auto px-4 py-8 pb-24 min-h-[60vh]">
    <!-- Breadcrumb -->
    <div class="text-xs font-bold text-gray-800 tracking-wider mb-8 uppercase">
      <RouterLink to="/" class="hover:text-[#3771C8] transition">HOME</RouterLink> / <span class="text-[#3771C8]">SEARCH</span>
    </div>

    <!-- Header -->
    <div class="mb-10">
      <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Search Results for "{{ route.query.q }}"</h1>
      <p class="text-gray-500 font-medium">
        {{ filteredProducts.length }} {{ filteredProducts.length === 1 ? 'result' : 'results' }} found
      </p>
    </div>

    <!-- Main Layout -->
    <div class="flex flex-col md:flex-row gap-8">
      
      <!-- Sidebar Filter -->
      <aside class="w-full md:w-64 flex-shrink-0">
        <div class="border border-gray-300 rounded-xl p-6 bg-white sticky top-24">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-extrabold text-gray-900 uppercase tracking-wider">FILTER</h2>
            <button @click="clearFilters" class="text-xs font-bold text-[#3771C8] hover:underline">Clear All</button>
          </div>

          <!-- Category -->
          <div class="mb-6">
            <h3 class="font-bold text-gray-900 mb-3 text-sm">Category</h3>
            <div class="relative">
              <select v-model="selectedCategory" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#3771C8] focus:border-[#3771C8] appearance-none bg-white">
                <option value="">All Categories</option>
                <option v-for="cat in productStore.categories" :key="cat.id" :value="cat.name">
                  {{ cat.name }}
                </option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
              </div>
            </div>
          </div>

          <!-- Brands -->
          <div class="mb-6">
            <h3 class="font-bold text-gray-900 mb-3 text-sm">Brand</h3>
            <div class="relative">
              <select v-model="selectedBrand" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#3771C8] focus:border-[#3771C8] appearance-none bg-white">
                <option value="">All Brands</option>
                <option v-for="brand in productStore.brands" :key="brand.id" :value="brand.name">
                  {{ brand.name }}
                </option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
              </div>
            </div>
          </div>

          <!-- Price -->
          <div class="mb-6">
            <h3 class="font-bold text-gray-900 mb-3 text-sm">Max Price</h3>
            <input type="range" min="100000" max="5000000" step="100000" v-model.number="priceRange" class="w-full accent-[#3771C8]" />
            <div class="text-xs font-bold text-gray-600 mt-2">Up to Rp {{ (priceRange / 1000).toLocaleString('id-ID') }}.000</div>
          </div>

        </div>
      </aside>

      <!-- Product Grid -->
      <div class="flex-1">
        <div v-if="loading" class="flex justify-center items-center h-64">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#3771C8]"></div>
        </div>
        <div v-else-if="filteredProducts.length > 0" class="grid grid-cols-2 lg:grid-cols-4 gap-6">
          <ProductCard v-for="product in filteredProducts" :key="product.id" :product="product" />
        </div>
        <div v-else class="text-center py-20 bg-gray-50 rounded-2xl border border-gray-100">
          <p class="text-gray-500 font-medium mb-6">No products match your filters.</p>
          <button @click="clearFilters" class="inline-block px-8 py-3 bg-[#3771C8] text-white font-bold rounded-xl hover:opacity-90 transition">
            Clear Filters
          </button>
        </div>
      </div>
      
    </div>
  </div>
</template>
