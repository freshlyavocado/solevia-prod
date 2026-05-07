<!-- 
  INFO FILE
  Nama: BrandsView.vue
  Fungsi: Halaman yang menampilkan daftar semua Brand yang tersedia dalam bentuk grid logo.
-->

<script setup lang="ts">
import { onMounted } from 'vue'
import { useProductStore } from '../stores/product'

const productStore = useProductStore()

onMounted(() => {
  if (productStore.brands.length === 0) {
    productStore.fetchBrands()
  }
})
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="text-xs font-bold text-gray-800 tracking-wider mb-8 uppercase">
      <RouterLink to="/" class="hover:text-[#3771C8] transition">HOME</RouterLink> / <span class="text-[#3771C8]">BRANDS</span>
    </div>

    <!-- Brands Grid -->
    <div v-if="productStore.brands.length === 0" class="text-center py-10 text-gray-500">
      Loading brands...
    </div>
    
    <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
      <RouterLink 
        v-for="brand in productStore.brands" 
        :key="brand.id" 
        :to="`/brands/${brand.id}`"
        class="flex flex-col items-center justify-center py-8 px-4 border border-gray-200 rounded-2xl hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
      >
        <img 
          :src="brand.logo_url ? `http://localhost:8000/storage/${brand.logo_url}` : 'https://placehold.co/200x100?text=' + brand.name" 
          :alt="brand.name" 
          class="h-16 object-contain mb-6 mix-blend-multiply"
        />
        <span class="text-sm font-bold text-gray-900">{{ brand.name }}</span>
      </RouterLink>
    </div>
  </div>
</template>
