<!-- 
  INFO FILE
  Nama: DefaultLayout.vue
  Fungsi: Kerangka dasar utama aplikasi yang membungkus konten dengan Navbar di atas dan Footer di bawah.
-->

<script setup lang="ts">
import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'
import { RouterView } from 'vue-router'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white">
    <Navbar />
    <main class="flex-grow">
      <RouterView />
    </main>
    <Footer />
  </div>
</template>
