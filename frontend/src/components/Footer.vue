<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { Instagram, Facebook, Twitter } from 'lucide-vue-next'
</script>

<template>
  <footer class="bg-[#003b8e] text-white py-12 mt-16">
    <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
      <!-- Logo -->
      <div>
        <h2 class="text-3xl font-extrabold tracking-tight mb-6">Solevia</h2>
        <ul class="space-y-4 text-base text-gray-100 font-medium">
          <li><RouterLink to="/" class="hover:text-white transition">Home</RouterLink></li>
          <li><RouterLink to="/size-guide" class="hover:text-white transition">Size Guide</RouterLink></li>
          <li><RouterLink to="/contact" class="hover:text-white transition">Contact Us</RouterLink></li>
        </ul>
      </div>

      <!-- Follow Us -->
      <div class="md:col-span-1">
        <h3 class="font-bold mb-4 uppercase text-sm tracking-wider">Follow Us</h3>
        <div class="flex space-x-4">
          <a href="#" class="bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-500 rounded-full p-2 hover:opacity-80 transition">
            <Instagram class="h-5 w-5 text-white" />
          </a>
          <a href="#" class="bg-[#3771C8] rounded-full p-2 hover:opacity-90 transition">
            <Facebook class="h-5 w-5 text-white" />
          </a>
          <a href="#" class="bg-black border border-white rounded-full p-2 hover:bg-gray-900 transition">
            <Twitter class="h-5 w-5 text-white" /> <!-- Using Twitter as placeholder for X -->
          </a>
        </div>
      </div>

      <!-- Map & Address -->
      <div class="md:col-span-2 flex flex-col md:items-end">
        <!-- Placeholder for map image, using a gray box for now or image if provided -->
        <div class="w-full max-w-sm h-32 bg-gray-200 rounded-lg overflow-hidden mb-3">
          <img src="/images/footer.png" alt="Map Location" class="w-full h-full object-cover" onerror="this.style.display='none'" />
        </div>
        <p class="text-sm text-gray-200 text-left max-w-sm font-medium">
          Jl. STM Pembangunan, Mrican, Caturtunggal, Kec. Depok, Kabupaten Sleman, Daerah Istimewa Yogyakarta 55281
        </p>
      </div>
    </div>

    <div class="container mx-auto px-4 mt-12 pt-6 border-t border-blue-800 text-center text-xs text-gray-300">
      Copyright © 2025 All rights reserved.
    </div>
  </footer>
</template>
