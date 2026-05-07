<!-- 
  INFO FILE
  Nama: AboutUsView.vue
  Fungsi: Halaman statis yang menampilkan informasi perusahaan dan "The Solevia Promise".
-->

<script setup lang="ts">
import { ShieldCheck, Truck, RotateCcw, Headphones } from 'lucide-vue-next'
</script>

<template>
  <div class="bg-white">
    <!-- Hero Section -->
    <div class="relative bg-gray-900 text-white overflow-hidden">
      <div class="absolute inset-0 opacity-40">
        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=2000&auto=format&fit=crop" alt="About Solevia" class="w-full h-full object-cover" />
      </div>
      <div class="container mx-auto px-4 py-24 md:py-32 relative z-10 text-center max-w-4xl">
        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-6">More Than Just Sneakers.</h1>
        <p class="text-lg md:text-xl text-gray-300 leading-relaxed font-medium">
          Solevia is your ultimate destination for authentic, high-performance, and stylish footwear. 
          We connect sneaker enthusiasts with the world's most sought-after brands.
        </p>
      </div>
    </div>

    <!-- Mission Section -->
    <div class="container mx-auto px-4 py-20">
      <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-6">Our Mission</h2>
        <p class="text-lg text-gray-600 leading-relaxed">
          At Solevia, we believe that the right pair of shoes can change your day, your workout, and your life. 
          Our mission is to provide a seamless, trustworthy, and premium shopping experience for every step you take. 
          Whether you are an athlete looking for peak performance or a trendsetter looking for the latest drop, we've got you covered.
        </p>
      </div>
    </div>

    <!-- The Solevia Promise -->
    <div class="bg-gray-50 py-20">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-16">The Solevia Promise</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 text-center">
          <!-- Item 1 -->
          <div class="flex flex-col items-center">
            <div class="w-16 h-16 bg-[#dbeafe] rounded-full flex items-center justify-center mb-6">
              <ShieldCheck class="w-8 h-8 text-[#3771C8]" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">100% Authentic</h3>
            <p class="text-gray-600 text-sm leading-relaxed">
              Every item sold on Solevia goes through a rigorous authentication process by our team of experts.
            </p>
          </div>
          <!-- Item 2 -->
          <div class="flex flex-col items-center">
            <div class="w-16 h-16 bg-[#dbeafe] rounded-full flex items-center justify-center mb-6">
              <Truck class="w-8 h-8 text-[#3771C8]" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Fast Shipping</h3>
            <p class="text-gray-600 text-sm leading-relaxed">
              We partner with top-tier logistics providers to ensure your kicks arrive fast and secure.
            </p>
          </div>
          <!-- Item 3 -->
          <div class="flex flex-col items-center">
            <div class="w-16 h-16 bg-[#dbeafe] rounded-full flex items-center justify-center mb-6">
              <RotateCcw class="w-8 h-8 text-[#3771C8]" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Easy Returns</h3>
            <p class="text-gray-600 text-sm leading-relaxed">
              Not the right fit? No problem. Our hassle-free return policy makes exchanging sizes a breeze.
            </p>
          </div>
          <!-- Item 4 -->
          <div class="flex flex-col items-center">
            <div class="w-16 h-16 bg-[#dbeafe] rounded-full flex items-center justify-center mb-6">
              <Headphones class="w-8 h-8 text-[#3771C8]" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">24/7 Support</h3>
            <p class="text-gray-600 text-sm leading-relaxed">
              Our dedicated support team is always here to help you with any questions or concerns.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="container mx-auto px-4 py-24 text-center">
      <h2 class="text-3xl font-extrabold text-gray-900 mb-6">Ready to Step Up?</h2>
      <p class="text-gray-600 mb-8 max-w-xl mx-auto">
        Join thousands of satisfied customers and elevate your sneaker game today.
      </p>
      <RouterLink to="/" class="inline-block px-8 py-4 bg-[#3771C8] text-white font-bold rounded-xl shadow-lg hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
        Shop Now
      </RouterLink>
    </div>
  </div>
</template>
