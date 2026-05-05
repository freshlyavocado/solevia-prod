<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Eye, EyeOff } from 'lucide-vue-next'

const router = useRouter()
const email = ref('')
const password = ref('')
const showPassword = ref(false)

const handleLogin = (e: Event) => {
  e.preventDefault()
  // Mock login action
  console.log('Logging in with', email.value, password.value)
  // Normally: authStore.login(...)
  router.push('/')
}
</script>

<template>
  <div>
    <!-- Tabs -->
    <div class="flex border-b border-gray-200 mb-8">
      <RouterLink to="/signup" class="flex-1 text-center py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition">Sign Up</RouterLink>
      <div class="flex-1 text-center py-3 text-sm font-bold border-b-2 border-black text-black">Log In</div>
    </div>

    <h2 class="text-xl font-bold mb-6">Log In</h2>

    <form @submit="handleLogin" class="space-y-4">
      <div>
        <input 
          v-model="email" 
          type="email" 
          placeholder="Email Address*" 
          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#3771C8] focus:border-[#3771C8]"
          required
        />
      </div>
      <div class="relative">
        <input 
          v-model="password" 
          :type="showPassword ? 'text' : 'password'" 
          placeholder="Password*" 
          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#3771C8] focus:border-[#3771C8] pr-12"
          required
        />
        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
          <EyeOff v-if="!showPassword" class="h-5 w-5" />
          <Eye v-else class="h-5 w-5" />
        </button>
      </div>

      <div class="flex justify-end">
        <a href="#" class="text-xs font-bold text-gray-700 hover:text-[#3771C8] transition">Forgot Password?</a>
      </div>

      <button type="submit" class="w-full bg-[#3771C8] text-white font-bold py-3 rounded-md hover:bg-[#3771C8] transition mt-4">
        Log In
      </button>
    </form>

    <div class="mt-8 relative flex items-center justify-center">
      <div class="border-t border-gray-200 w-full absolute"></div>
      <span class="bg-white px-4 text-xs text-gray-400 relative z-10">OR</span>
    </div>

    <div class="text-center mt-4 mb-6 text-xs text-gray-600">Log In with</div>
    
    <div class="flex justify-center space-x-4">
      <!-- Social Buttons Mock -->
      <button class="w-10 h-10 border border-gray-300 rounded-md flex items-center justify-center hover:bg-gray-50 transition">
        <span class="font-bold text-red-500">G</span>
      </button>
      <button class="w-10 h-10 border border-gray-300 rounded-md flex items-center justify-center hover:bg-gray-50 transition">
        <span class="font-bold text-[#3771C8]">f</span>
      </button>
      <button class="w-10 h-10 border border-gray-300 rounded-md flex items-center justify-center hover:bg-gray-50 transition">
        <span class="font-bold text-black">X</span>
      </button>
    </div>

    <div class="text-center mt-8 text-xs text-gray-600">
      Need an account? <RouterLink to="/signup" class="text-[#3771C8] font-bold hover:underline">Sign Up</RouterLink>
    </div>
  </div>
</template>
