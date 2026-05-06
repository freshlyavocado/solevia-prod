<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useProductStore } from '../stores/product'
import ProductCard from '../components/ProductCard.vue'

const route = useRoute()
const productStore = useProductStore()
const loading = ref(true)

// Filter states
const selectedBrand = ref<string>('')
const priceRange = ref<number>(5000000)

const fetchProducts = async () => {
  loading.value = true
  try {
    if (productStore.products.length === 0) {
      await productStore.fetchProducts()
    }
    if (productStore.brands.length === 0) {
      await productStore.fetchBrands()
    }
  } catch (error) {
    console.error("Error fetching data", error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchProducts()
})

const categoryName = computed(() => {
  // Extract category from path, e.g. "/men" -> "Men"
  const path = route.path.replace('/', '')
  return path.charAt(0).toUpperCase() + path.slice(1)
})

const filteredProducts = computed(() => {
  return productStore.products.filter(p => {
    // Match category
    const catNameDb = p.category?.name.toLowerCase() || ''
    const currentCat = categoryName.value.toLowerCase()
    // Exact match or match with 's (e.g. Men's)
    if (catNameDb !== currentCat && catNameDb !== currentCat + "'s" && catNameDb !== currentCat + "s") {
      return false
    }
    
    // Match brands
    if (selectedBrand.value && p.brand) {
      if (p.brand.name !== selectedBrand.value) return false
    }

    // Match price range
    const effectivePrice = p.discount_price || p.price
    if (effectivePrice > priceRange.value) return false

    return true
  })
})

// Reset filters when route changes (e.g. from /men to /women)
watch(() => route.path, () => {
  selectedBrand.value = ''
  priceRange.value = 5000000
})
</script>

<template>
  <div class="container mx-auto px-4 py-8 pb-24">
    <!-- Breadcrumb -->
    <div class="text-xs font-bold text-gray-900 uppercase tracking-widest mb-6">
      <RouterLink to="/" class="hover:text-[#3771C8] transition">HOME</RouterLink> / 
      <span class="text-[#3771C8]">{{ categoryName }}</span>
    </div>

    <!-- Banner -->
    <div class="bg-[#dbeafe] rounded-2xl p-8 md:p-12 mb-12">
      <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">{{ categoryName }}'s Collection</h1>
      <p class="text-sm md:text-base text-gray-800 leading-relaxed max-w-4xl">
        Discover the best gear crafted specifically for {{ categoryName.toLowerCase() }}. From top performance running shoes to stylish streetwear, find exactly what you need to elevate your game.
      </p>
    </div>

    <!-- Main Layout -->
    <div class="flex flex-col md:flex-row gap-8">
      
      <!-- Sidebar Filter -->
      <aside class="w-full md:w-64 flex-shrink-0">
        <div class="border border-gray-300 rounded-xl p-6 bg-white sticky top-24">
          <h2 class="text-xl font-extrabold text-gray-900 mb-6 uppercase tracking-wider">FILTER</h2>

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
        <div v-else class="text-center py-20 bg-gray-50 rounded-2xl">
          <p class="text-gray-500 font-medium">No products found matching your filters.</p>
          <button @click="selectedBrand = ''; priceRange = 5000000" class="mt-4 text-[#3771C8] font-bold hover:underline">Clear Filters</button>
        </div>
      </div>

    </div>
  </div>
</template>
