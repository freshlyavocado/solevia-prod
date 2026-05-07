<!-- 
  INFO FILE
  Nama: SignupView.vue
  Fungsi: Halaman otentikasi untuk pengguna baru mendaftar (register) akun.
-->

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { Eye, EyeOff } from 'lucide-vue-next'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const firstName = ref('')
const lastName = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const handleSignup = async (e: Event) => {
  e.preventDefault()
  if (password.value !== confirmPassword.value) {
    alert("Passwords do not match!")
    return
  }
  
  try {
    await authStore.register({
      name: `${firstName.value} ${lastName.value}`,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value
    })
    alert('Sign up successful!')
    router.push('/')
  } catch (error: any) {
    console.error("Signup error:", error)
    alert(error.response?.data?.message || 'Failed to sign up. Please check your inputs.')
  }
}
</script>

<template>
  <div>
    <!-- Tabs -->
    <div class="flex border-b border-gray-200 mb-8">
      <div class="flex-1 text-center py-3 text-sm font-bold border-b-2 border-black text-black">Sign Up</div>
      <RouterLink to="/login" class="flex-1 text-center py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition">Log In</RouterLink>
    </div>

    <h2 class="text-xl font-bold mb-6">Sign Up</h2>

    <form @submit="handleSignup" class="space-y-4">
      <div>
        <input 
          v-model="firstName" 
          type="text" 
          placeholder="First Name*" 
          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#3771C8] focus:border-[#3771C8]"
          required
        />
      </div>
      <div>
        <input 
          v-model="lastName" 
          type="text" 
          placeholder="Last Name*" 
          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#3771C8] focus:border-[#3771C8]"
          required
        />
      </div>
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
      
      <p class="text-[10px] font-bold text-gray-500 mt-1 mb-3">At least 8 characters, 1 uppercase letter, 1 number & 1 symbol</p>

      <div class="relative mt-2">
        <input 
          v-model="confirmPassword" 
          :type="showConfirmPassword ? 'text' : 'password'" 
          placeholder="Confirm Password*" 
          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-[#3771C8] focus:border-[#3771C8] pr-12"
          required
        />
        <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
          <EyeOff v-if="!showConfirmPassword" class="h-5 w-5" />
          <Eye v-else class="h-5 w-5" />
        </button>
      </div>

      <button type="submit" class="w-full bg-[#3771C8] text-white font-bold py-3 rounded-md hover:bg-[#3771C8] transition mt-6">
        Sign Up
      </button>
    </form>

    <div class="mt-8 relative flex items-center justify-center">
      <div class="border-t border-gray-200 w-full absolute"></div>
      <span class="bg-white px-4 text-xs text-gray-400 relative z-10">OR</span>
    </div>

    <div class="text-center mt-4 mb-6 text-xs text-gray-600">Sign up with</div>
    
    <div class="flex justify-center space-x-4">
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
      Already have an account? <RouterLink to="/login" class="text-[#3771C8] font-bold hover:underline">Log In</RouterLink>
    </div>
  </div>
</template>
