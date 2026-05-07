<!-- 
  INFO FILE
  Nama: BrandDetailView.vue
  Fungsi: Halaman dinamis yang menampilkan detail spesifik sebuah Brand beserta produk-produknya yang bisa difilter.
-->

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useProductStore } from '../stores/product'
import ProductCard from '../components/ProductCard.vue'
import api from '../services/api'
import type { Brand } from '../types'

const route = useRoute()
const productStore = useProductStore()
const brand = ref<Brand | null>(null)
const loading = ref(true)

// Filter states
const selectedCategory = ref<string>('')
const priceRange = ref<number>(5000000)

const fetchBrandAndProducts = async () => {
  loading.value = true
  try {
    const { data } = await api.get(`/brands/${route.params.id}`)
    brand.value = data
    if (productStore.products.length === 0) {
      await productStore.fetchProducts()
    }
    if (productStore.categories.length === 0) {
      await productStore.fetchCategories()
    }
  } catch (error) {
    console.error("Error fetching brand details", error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchBrandAndProducts()
})

const filteredProducts = computed(() => {
  if (!brand.value) return []
  return productStore.products.filter(p => {
    // Match brand
    if (p.brand_id !== brand.value?.id) return false
    
    // Match categories
    if (selectedCategory.value && p.category) {
      if (p.category.name !== selectedCategory.value) return false
    }

    // Match price range
    const effectivePrice = p.discount_price || p.price
    if (effectivePrice > priceRange.value) return false

    return true
  })
})
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#3771C8]"></div>
    </div>
    
    <div v-else-if="brand">
      <!-- Breadcrumb -->
      <div class="text-xs font-bold text-gray-900 uppercase tracking-widest mb-4">
        <RouterLink to="/" class="hover:text-[#3771C8] transition">HOME</RouterLink> / 
        <RouterLink to="/brands" class="hover:text-[#3771C8] transition">BRANDS</RouterLink> / 
        <span class="text-[#3771C8]">{{ brand.name }}</span>
      </div>

      <!-- Banner -->
      <div class="bg-[#dbeafe] rounded-2xl p-8 md:p-12 mb-12">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">{{ brand.name }}</h1>
        <p class="text-sm md:text-base text-gray-800 leading-relaxed max-w-4xl">
          {{ brand.description || `Explore the latest ${brand.name} collection. Known for innovation and style, ${brand.name} brings you high-performance gear crafted to elevate your daily routine.` }}
        </p>
      </div>

      <!-- Main Layout -->
      <div class="flex flex-col md:flex-row gap-8">
        
        <!-- Sidebar Filter -->
        <aside class="w-full md:w-64 flex-shrink-0">
          <div class="border border-gray-300 rounded-xl p-6 bg-white sticky top-24">
            <h2 class="text-xl font-extrabold text-gray-900 mb-6 uppercase tracking-wider">FILTER</h2>

            <!-- Categories -->
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
          <div v-if="filteredProducts.length > 0" class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <ProductCard v-for="product in filteredProducts" :key="product.id" :product="product" />
          </div>
          <div v-else class="text-center py-20">
            <p class="text-gray-500 font-medium">No products found matching your filters.</p>
            <button @click="selectedCategory = ''; priceRange = 5000000" class="mt-4 text-[#3771C8] font-bold hover:underline">Clear Filters</button>
          </div>
        </div>

      </div>
    </div>

    <div v-else class="text-center py-20 text-gray-500">
      Brand not found.
    </div>
  </div>
</template>
