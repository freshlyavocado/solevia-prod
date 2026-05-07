<!-- 
  INFO FILE
  Nama: HomeView.vue
  Fungsi: Halaman beranda utama yang menampilkan banner promosi, brand populer, dan highlight produk.
-->

<script setup lang="ts">
import { onMounted, ref, onUnmounted } from 'vue'
import { useProductStore } from '../stores/product'
import ProductCard from '../components/ProductCard.vue'

const productStore = useProductStore()

// Carousel Logic
const currentSlide = ref(0)
const slides = [
  '/images/carousel/image_2.png',
  '/images/carousel/image_3.png',
  '/images/carousel/image_4.png'
]
let slideInterval: number | undefined

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % slides.length
}

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length
}

const goToSlide = (index: number) => {
  currentSlide.value = index
}

onMounted(() => {
  productStore.fetchProducts()
  productStore.fetchBrands()
  
  // Auto-advance carousel
  slideInterval = window.setInterval(nextSlide, 5000)
})

onUnmounted(() => {
  if (slideInterval) clearInterval(slideInterval)
})
</script>

<template>
  <div class="w-full pb-20">
    <!-- Hero Banner Carousel -->
    <section class="container mx-auto px-4 mt-6">
      <div class="relative w-full h-[400px] md:h-[500px] rounded-2xl overflow-hidden group">
        
        <!-- Slides -->
        <div 
          v-for="(slide, index) in slides" 
          :key="index"
          class="absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out"
          :class="currentSlide === index ? 'opacity-100 z-10' : 'opacity-0 z-0'"
        >
          <img :src="slide" class="w-full h-full object-cover object-center" alt="Banner Promotion" />
        </div>

        <!-- Carousel Navigation Arrows -->
        <button 
          @click="prevSlide" 
          class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-white/50 hover:bg-white text-gray-800 p-2 md:p-3 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-lg"
          aria-label="Previous slide"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
          </svg>
        </button>
        <button 
          @click="nextSlide" 
          class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-white/50 hover:bg-white text-gray-800 p-2 md:p-3 rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-lg"
          aria-label="Next slide"
        >
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
          </svg>
        </button>

        <!-- Carousel Dots -->
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex space-x-2">
          <button 
            v-for="(_, index) in slides" 
            :key="index"
            @click="goToSlide(index)"
            class="w-3 h-3 rounded-full transition-all duration-300"
            :class="currentSlide === index ? 'bg-white w-8' : 'bg-white/50 hover:bg-white/80'"
            aria-label="Go to slide"
          ></button>
        </div>
      </div>
    </section>

    <!-- Recommended For You -->
    <section class="container mx-auto px-4 mt-16">
      <h3 class="text-xl font-bold text-gray-900 mb-6 border-l-4 border-[#3771C8] pl-3">Recommended For You</h3>
      
      <div v-if="productStore.loading" class="flex justify-center py-10">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#3771C8]"></div>
      </div>
      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
        <ProductCard v-for="product in productStore.products.slice(0, 6)" :key="product.id" :product="product" />
      </div>
    </section>

    <!-- Trending Shoes -->
    <section class="container mx-auto px-4 mt-16">
      <h3 class="text-xl font-bold text-gray-900 mb-6 border-l-4 border-[#3771C8] pl-3">Trending Shoes</h3>
      
      <div v-if="productStore.loading" class="flex justify-center py-10">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#3771C8]"></div>
      </div>
      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
        <ProductCard v-for="product in productStore.products.slice(6, 12)" :key="product.id" :product="product" />
      </div>
    </section>

    <!-- Popular Brands -->
    <section class="container mx-auto px-4 mt-16">
      <h3 class="text-xl font-bold text-gray-900 mb-6 border-l-4 border-[#3771C8] pl-3">Popular Brands</h3>
      
      <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
        <RouterLink 
          v-for="brand in productStore.brands.slice(0, 5)" 
          :key="brand.id" 
          :to="`/brands/${brand.id}`"
          class="flex flex-col items-center justify-center p-6 border border-gray-200 bg-white rounded-xl hover:shadow-lg hover:-translate-y-1 hover:border-[#3771C8] transition-all duration-300 group"
        >
          <img 
            :src="brand.logo_url ? `http://localhost:8000/storage/${brand.logo_url}` : 'https://placehold.co/200x100?text=' + brand.name" 
            :alt="brand.name" 
            class="h-12 object-contain mb-4 mix-blend-multiply transition-all duration-300"
          />
          <span class="text-sm font-bold text-gray-900">{{ brand.name }}</span>
        </RouterLink>
      </div>
    </section>

    <!-- Static Banner (Novablast or similar) -->
    <section class="container mx-auto px-4 mt-16">
      <RouterLink to="/new" class="block relative w-full h-[300px] md:h-[400px] rounded-2xl overflow-hidden group shadow-md hover:shadow-xl transition-all duration-300">
        <img src="/images/static/novablast.png" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700" alt="Explore the series" />
        <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition-colors duration-300"></div>
      </RouterLink>
    </section>

    <!-- New to Solevia -->
    <section class="container mx-auto px-4 mt-16">
      <h3 class="text-xl font-bold text-gray-900 mb-6 border-l-4 border-[#3771C8] pl-3">New to Solevia</h3>
      
      <div v-if="productStore.loading" class="flex justify-center py-10">
        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#3771C8]"></div>
      </div>
      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
        <!-- Reversing product array visually to simulate "new" -->
        <ProductCard v-for="product in [...productStore.products].reverse().slice(0, 6)" :key="product.id" :product="product" />
      </div>
    </section>
  </div>
</template>
