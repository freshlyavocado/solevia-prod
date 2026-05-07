<!-- 
  INFO FILE
  Nama: App.vue
  Fungsi: Komponen akar (Root) Vue tempat Vue Router disuntikkan (<RouterView>).
-->

<script setup lang="ts">
// Mengimpor RouterView. Ini adalah "lubang" tempat router akan memasukkan komponen halaman 
// secara dinamis (seperti HomeView, AboutUsView, dll) tergantung pada URL saat ini.
import { RouterView } from 'vue-router'
</script>

<template>
  <!-- 
    Container utama pembungkus seluruh aplikasi.
    min-h-screen: Memastikan tinggi minimal adalah setinggi layar penuh.
    bg-gray-50: Memberi warna latar belakang abu-abu sangat terang secara default.
    text-gray-900: Memberi warna teks default hitam keabuan.
    font-sans: Memastikan jenis huruf bawaan (biasanya Poppins/Inter sesuai Tailwind config)
  -->
  <div class="min-h-screen bg-gray-50 text-gray-900 font-sans">
    <!-- Di sinilah komponen-komponen halaman akan digonta-ganti oleh Vue Router secara otomatis -->
    <RouterView />
  </div>
</template>
