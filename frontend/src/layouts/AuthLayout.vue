<!-- 
  INFO FILE
  Nama: AuthLayout.vue
  Fungsi: Kerangka dasar khusus untuk halaman otentikasi (tanpa Navbar/Footer utuh).
-->

<script setup lang="ts">
import { RouterView, RouterLink } from 'vue-router'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-[#dbeafe]">
    <div class="flex justify-center bg-white py-6 shadow-sm">
      <RouterLink to="/">
        <img src="/images/logo.png" alt="Solevia" class="h-12" />
      </RouterLink>
    </div>
    <div class="flex-grow flex items-center justify-center p-4">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-8">
        <RouterView />
      </div>
    </div>
  </div>
</template>
