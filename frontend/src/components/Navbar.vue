<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { Search, Heart, ShoppingCart, User } from 'lucide-vue-next'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const router = useRouter()
const searchQuery = ref('')

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'search', query: { q: searchQuery.value } })
  }
}
</script>

<template>
  <header class="w-full bg-white sticky top-0 z-50 shadow-sm">
    <!-- Top Navbar -->
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
      <!-- Logo -->
      <RouterLink to="/" class="flex items-center">
        <img src="/images/logo.png" alt="Solevia" class="h-6.5" />
      </RouterLink>

      <!-- Search Bar -->
      <div class="hidden md:flex flex-1 max-w-2xl mx-6 relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <Search class="h-5 w-5 text-gray-400" />
        </div>
        <input
          v-model="searchQuery"
          @keyup.enter="handleSearch"
          type="text"
          class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-full leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-[#3771C8] focus:ring-1 focus:ring-[#3771C8] sm:text-sm transition duration-150 ease-in-out"
          placeholder="Search for brand, etc."
        />
      </div>

      <!-- Icons and About Us -->
      <div class="flex items-center gap-8 mr-8">
        <RouterLink to="/about" class="text-sm font-semibold text-gray-900 hover:text-[#3771C8] transition whitespace-nowrap">
          About Us
        </RouterLink>
        <RouterLink to="/wishlist" class="text-gray-900 hover:text-[#3771C8] transition">
          <Heart class="h-6 w-6" />
        </RouterLink>
        <RouterLink to="/cart" class="text-gray-900 hover:text-[#3771C8] transition">
          <ShoppingCart class="h-6 w-6" />
        </RouterLink>
        
        <RouterLink v-if="!authStore.isAuthenticated" to="/login" class="px-5 py-1.5 border border-gray-300 rounded-full text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
          Login
        </RouterLink>
        <RouterLink v-else to="/profile" class="text-gray-900 hover:text-[#3771C8] transition">
          <User class="h-6 w-6" />
        </RouterLink>
      </div>
    </div>

    <!-- Secondary Navbar -->
    <div class="bg-[#dbeafe] w-full">
      <div class="container mx-auto px-4">
        <nav class="flex justify-center space-x-30 py-3">
          <RouterLink to="/brands" class="text-base font-bold text-gray-900 hover:text-[#3771C8] transition uppercase">Brands</RouterLink>
          <RouterLink to="/deals" class="text-base font-bold text-gray-900 hover:text-[#3771C8] transition uppercase">Deals</RouterLink>
          <RouterLink to="/men" class="text-base font-bold text-gray-900 hover:text-[#3771C8] transition uppercase">Men</RouterLink>
          <RouterLink to="/women" class="text-base font-bold text-gray-900 hover:text-[#3771C8] transition uppercase">Women</RouterLink>
          <RouterLink to="/kids" class="text-base font-bold text-gray-900 hover:text-[#3771C8] transition uppercase">Kids</RouterLink>
        </nav>
      </div>
    </div>
  </header>
</template>
