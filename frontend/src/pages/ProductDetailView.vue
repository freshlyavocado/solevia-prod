<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { Heart, Share2, Plus, Minus, ChevronLeft, ChevronRight } from 'lucide-vue-next'
import api from '../services/api'
import type { Product } from '../types'

const route = useRoute()
const product = ref<Product | null>(null)
const loading = ref(true)
const selectedImageIndex = ref(0)
const thumbnailStartIndex = ref(0)
const selectedSize = ref<string | null>(null)
const quantity = ref(1)

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(price)
}

const fetchProduct = async () => {
  loading.value = true
  try {
    const { data } = await api.get(`/products/${route.params.slug}`)
    product.value = data
  } catch (error) {
    console.error("Error fetching product details", error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchProduct()
})

const increaseQuantity = () => {
  quantity.value++
}

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const activeImage = computed(() => {
  if (!product.value?.images || product.value.images.length === 0) {
    return 'https://placehold.co/600x600?text=No+Image'
  }
  return `http://localhost:8000/storage/${product.value.images[selectedImageIndex.value]?.image_url}`
})

// Thumbnails Slider Logic
const visibleThumbnails = computed(() => {
  if (!product.value?.images) return []
  return product.value.images.slice(thumbnailStartIndex.value, thumbnailStartIndex.value + 4)
})

const nextThumbnails = () => {
  if (product.value?.images && thumbnailStartIndex.value + 4 < product.value.images.length) {
    thumbnailStartIndex.value++
  }
}

const prevThumbnails = () => {
  if (thumbnailStartIndex.value > 0) {
    thumbnailStartIndex.value--
  }
}

// Extract unique sizes from variants
const availableSizes = computed(() => {
  if (!product.value?.variants) return []
  const sizes = product.value.variants.map(v => v.size).filter(Boolean) as string[]
  return [...new Set(sizes)]
})
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#3771C8]"></div>
    </div>
    
    <div v-else-if="product" class="grid grid-cols-1 md:grid-cols-2 gap-12">
      <!-- Left Column: Images -->
      <div class="flex flex-col">
        <div class="relative w-full aspect-square bg-white rounded-xl mb-4 overflow-hidden flex items-center justify-center p-8 group">
          <button class="absolute top-4 right-4 z-10 p-2 text-gray-400 hover:text-[#3771C8] transition">
            <Heart class="h-6 w-6" />
          </button>
          <img :src="activeImage" :alt="product.name" class="w-full h-full object-contain mix-blend-multiply transition-transform duration-500 group-hover:scale-105" />
        </div>
        
        <!-- Thumbnails Slider -->
        <div v-if="product.images && product.images.length > 0" class="relative flex items-center">
          <button 
            @click="prevThumbnails" 
            :disabled="thumbnailStartIndex === 0"
            class="absolute -left-4 z-10 p-1 bg-white rounded-full shadow-md text-gray-500 hover:text-[#3771C8] disabled:opacity-30 disabled:hover:text-gray-500 transition-colors"
          >
            <ChevronLeft class="w-5 h-5" />
          </button>
          
          <div class="flex gap-4 w-full px-2">
            <button 
              v-for="(img, idx) in visibleThumbnails" 
              :key="img.id"
              @click="selectedImageIndex = thumbnailStartIndex + idx"
              class="flex-1 aspect-square rounded-xl bg-white border-2 flex items-center justify-center p-2 transition-all"
              :class="selectedImageIndex === (thumbnailStartIndex + idx) ? 'border-[#3771C8]' : 'border-gray-200 hover:border-[#3771C8]'"
            >
              <img :src="`http://localhost:8000/storage/${img.image_url}`" :alt="`${product.name} - image ${thumbnailStartIndex + idx + 1}`" class="w-full h-full object-contain mix-blend-multiply" />
            </button>
          </div>
          
          <button 
            @click="nextThumbnails" 
            :disabled="product.images && thumbnailStartIndex + 4 >= product.images.length"
            class="absolute -right-4 z-10 p-1 bg-white rounded-full shadow-md text-gray-500 hover:text-[#3771C8] disabled:opacity-30 disabled:hover:text-gray-500 transition-colors"
          >
            <ChevronRight class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Right Column: Details -->
      <div class="flex flex-col">
        <div class="border border-gray-300 rounded-2xl p-6 md:p-8 bg-white">
          <span class="text-sm font-bold text-[#3771C8] uppercase tracking-wide block mb-2 hover:opacity-80 transition">{{ product.brand?.name || 'Brand' }}</span>
          <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 mb-6 leading-tight">{{ product.name }}</h1>
          
          <div class="text-2xl font-bold text-gray-900 mb-8">{{ formatPrice(product.price) }}</div>

          <!-- Size Selection -->
          <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
              <span class="text-sm font-bold text-gray-900">Size</span>
              <a href="#" class="text-sm font-bold text-[#3771C8] hover:underline flex items-center transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                Size Guide
              </a>
            </div>
            <div v-if="availableSizes.length > 0" class="flex flex-wrap gap-3">
              <button 
                v-for="size in availableSizes" 
                :key="size"
                @click="selectedSize = size"
                class="px-5 py-2 border rounded-md text-sm font-bold transition-colors"
                :class="selectedSize === size ? 'border-[#3771C8] text-[#3771C8] bg-blue-50' : 'border-gray-300 text-gray-700 hover:border-[#3771C8] hover:text-[#3771C8]'"
              >
                {{ size }}
              </button>
            </div>
            <div v-else class="text-sm text-gray-500">
              One Size
            </div>
          </div>

          <!-- Quantity -->
          <div class="mb-10">
            <span class="text-sm font-bold text-gray-900 block mb-4">Quantity</span>
            <div class="flex items-center w-32 border border-[#3771C8]/30 rounded-full bg-blue-50/50 p-1 transition-colors hover:border-[#3771C8]">
              <button @click="decreaseQuantity" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-[#3771C8] rounded-full transition" :disabled="quantity <= 1">
                <Minus class="w-4 h-4" />
              </button>
              <div class="flex-1 text-center font-bold text-[#3771C8] text-sm">
                {{ quantity }}
              </div>
              <button @click="increaseQuantity" class="w-8 h-8 flex items-center justify-center bg-[#3771C8] text-white rounded-full hover:opacity-90 transition">
                <Plus class="w-4 h-4" />
              </button>
            </div>
          </div>

          <!-- Share -->
          <div class="mb-8">
            <button class="flex items-center text-[#3771C8] hover:opacity-80 font-medium text-sm transition-opacity">
              <Share2 class="w-4 h-4 mr-2" />
              Share
            </button>
          </div>

          <!-- Description -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-bold text-gray-900 mb-3">Description</h3>
            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
              {{ product.description || 'No description available for this product.' }}
            </p>
          </div>

        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-2 gap-4 mt-6">
          <button class="py-4 border border-[#3771C8]/40 bg-white text-[#3771C8] font-bold rounded-xl hover:border-[#3771C8] hover:bg-blue-50 transition shadow-sm">
            Add to Favorite
          </button>
          <button class="py-4 bg-[#3771C8] text-white font-bold rounded-xl hover:opacity-90 transition shadow-sm">
            Buy Now
          </button>
        </div>
      </div>
    </div>
    
    <div v-else class="text-center py-20 text-gray-500">
      Product not found.
    </div>
  </div>
</template>
