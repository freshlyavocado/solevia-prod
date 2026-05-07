<!-- 
  INFO FILE
  Nama: ContactUsView.vue
  Fungsi: Halaman statis yang berisi informasi kontak dan formulir untuk menghubungi layanan pelanggan.
-->

<script setup lang="ts">
import { ref } from 'vue'

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: ''
})

const submitForm = () => {
  alert('Thank you for contacting us! We will get back to you shortly.')
  form.value = { name: '', email: '', subject: '', message: '' }
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="text-xs font-bold text-gray-800 tracking-wider mb-8 uppercase">
      <RouterLink to="/" class="hover:text-[#3771C8] transition">HOME</RouterLink> / <span class="text-[#3771C8]">CONTACT US</span>
    </div>

    <div class="max-w-4xl mx-auto">
      <div class="text-center mb-12">
      <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Contact Us</h1>
      <p class="text-gray-600">Have a question? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
      <!-- Contact Info -->
      <div class="bg-gray-50 p-8 md:p-12 border-r border-gray-100">
        <h3 class="text-xl font-bold text-gray-900 mb-6">Get In Touch</h3>
        
        <div class="space-y-6">
          <div>
            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-1">Address</h4>
            <p class="text-gray-600 text-sm leading-relaxed">
              Jl. STM Pembangunan, Mrican, Caturtunggal,<br/>
              Kec. Depok, Kabupaten Sleman,<br/>
              Yogyakarta 55281
            </p>
          </div>
          
          <div>
            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-1">Email</h4>
            <a href="mailto:support@solevia.com" class="text-[#3771C8] hover:underline text-sm">support@solevia.com</a>
          </div>
          
          <div>
            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-1">Phone</h4>
            <p class="text-gray-600 text-sm">+62 812 3456 7890</p>
          </div>
          
          <div>
            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-1">Working Hours</h4>
            <p class="text-gray-600 text-sm">Mon - Fri: 9:00 AM - 6:00 PM</p>
          </div>
        </div>
      </div>

      <!-- Contact Form -->
      <div class="p-8 md:p-12">
        <form @submit.prevent="submitForm" class="space-y-5">
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Name</label>
            <input v-model="form.name" type="text" required class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-1 focus:ring-[#3771C8] focus:border-[#3771C8] outline-none transition" placeholder="John Doe">
          </div>
          
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
            <input v-model="form.email" type="email" required class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-1 focus:ring-[#3771C8] focus:border-[#3771C8] outline-none transition" placeholder="john@example.com">
          </div>
          
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Subject</label>
            <input v-model="form.subject" type="text" required class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-1 focus:ring-[#3771C8] focus:border-[#3771C8] outline-none transition" placeholder="How can we help?">
          </div>
          
          <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Message</label>
            <textarea v-model="form.message" required rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-1 focus:ring-[#3771C8] focus:border-[#3771C8] outline-none transition resize-none" placeholder="Your message here..."></textarea>
          </div>
          
          <button type="submit" class="w-full bg-[#3771C8] text-white font-bold py-3 rounded-md hover:opacity-90 transition mt-2 shadow-md">
            Send Message
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
</template>
