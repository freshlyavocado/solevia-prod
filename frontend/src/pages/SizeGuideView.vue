<!-- 
  INFO FILE
  Nama: SizeGuideView.vue
  Fungsi: Halaman statis yang berisi tabel panduan ukuran sepatu (Men & Women).
-->

<script setup lang="ts">
// Simple static view
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="text-xs font-bold text-gray-800 tracking-wider mb-8 uppercase">
      <RouterLink to="/" class="hover:text-[#3771C8] transition">HOME</RouterLink> / <span class="text-[#3771C8]">SIZE GUIDE</span>
    </div>

    <div class="max-w-4xl mx-auto">
      <div class="text-center mb-12">
      <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Size Guide</h1>
      <p class="text-gray-600 max-w-2xl mx-auto">
        Use the charts below to help you determine the perfect fit for your next pair of sneakers. 
        Measurements are approximate and may vary slightly by brand.
      </p>
    </div>

    <div class="space-y-12">
      <!-- Men's Chart -->
      <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
        <h2 class="text-xl font-bold text-gray-900 mb-6 border-l-4 border-[#3771C8] pl-3">Men's Footwear</h2>
        <table class="w-full text-left text-sm text-gray-700">
          <thead class="bg-gray-50 text-gray-900 uppercase font-bold">
            <tr>
              <th class="px-4 py-3 rounded-tl-lg">US</th>
              <th class="px-4 py-3">UK</th>
              <th class="px-4 py-3">EU</th>
              <th class="px-4 py-3 rounded-tr-lg">CM</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr><td class="px-4 py-3">7</td><td class="px-4 py-3">6</td><td class="px-4 py-3">40</td><td class="px-4 py-3">25</td></tr>
            <tr><td class="px-4 py-3">8</td><td class="px-4 py-3">7</td><td class="px-4 py-3">41</td><td class="px-4 py-3">26</td></tr>
            <tr><td class="px-4 py-3">9</td><td class="px-4 py-3">8</td><td class="px-4 py-3">42.5</td><td class="px-4 py-3">27</td></tr>
            <tr><td class="px-4 py-3">10</td><td class="px-4 py-3">9</td><td class="px-4 py-3">44</td><td class="px-4 py-3">28</td></tr>
            <tr><td class="px-4 py-3">11</td><td class="px-4 py-3">10</td><td class="px-4 py-3">45</td><td class="px-4 py-3">29</td></tr>
            <tr><td class="px-4 py-3">12</td><td class="px-4 py-3">11</td><td class="px-4 py-3">46</td><td class="px-4 py-3">30</td></tr>
          </tbody>
        </table>
      </div>

      <!-- Women's Chart -->
      <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 overflow-x-auto">
        <h2 class="text-xl font-bold text-gray-900 mb-6 border-l-4 border-[#3771C8] pl-3">Women's Footwear</h2>
        <table class="w-full text-left text-sm text-gray-700">
          <thead class="bg-gray-50 text-gray-900 uppercase font-bold">
            <tr>
              <th class="px-4 py-3 rounded-tl-lg">US</th>
              <th class="px-4 py-3">UK</th>
              <th class="px-4 py-3">EU</th>
              <th class="px-4 py-3 rounded-tr-lg">CM</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr><td class="px-4 py-3">5</td><td class="px-4 py-3">2.5</td><td class="px-4 py-3">35.5</td><td class="px-4 py-3">22</td></tr>
            <tr><td class="px-4 py-3">6</td><td class="px-4 py-3">3.5</td><td class="px-4 py-3">36.5</td><td class="px-4 py-3">23</td></tr>
            <tr><td class="px-4 py-3">7</td><td class="px-4 py-3">4.5</td><td class="px-4 py-3">38</td><td class="px-4 py-3">24</td></tr>
            <tr><td class="px-4 py-3">8</td><td class="px-4 py-3">5.5</td><td class="px-4 py-3">39</td><td class="px-4 py-3">25</td></tr>
            <tr><td class="px-4 py-3">9</td><td class="px-4 py-3">6.5</td><td class="px-4 py-3">40.5</td><td class="px-4 py-3">26</td></tr>
            <tr><td class="px-4 py-3">10</td><td class="px-4 py-3">7.5</td><td class="px-4 py-3">42</td><td class="px-4 py-3">27</td></tr>
          </tbody>
        </table>
      </div>
      
      <!-- Tips -->
      <div class="bg-[#dbeafe] p-8 rounded-2xl mt-8">
        <h3 class="text-lg font-bold text-gray-900 mb-3">How to Measure</h3>
        <p class="text-gray-800 text-sm leading-relaxed mb-4">
          Place your foot on a flat surface with your heel against a straight edge or wall. Place a ruler beside your foot touching the straight edge your heel is also touching, then take the length in millimeters from the tip of your longest toe to your heel.
        </p>
        <p class="text-gray-800 text-sm leading-relaxed">
          <span class="font-bold">Pro Tip:</span> Always measure your feet in the afternoon, as feet tend to swell throughout the day.
        </p>
      </div>
    </div>
  </div>
</div>
</template>
