import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'
import type { User } from '../types'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(JSON.parse(localStorage.getItem('user') || 'null'))
  const token = ref<string | null>(localStorage.getItem('token') || null)

  const isAuthenticated = computed(() => !!token.value)

  async function login(credentials: Record<string, any>) {
    const { data } = await api.post('/login', credentials)
    user.value = data.user
    token.value = data.token
    localStorage.setItem('user', JSON.stringify(data.user))
    localStorage.setItem('token', data.token)
  }

  async function register(payload: Record<string, any>) {
    const { data } = await api.post('/register', payload)
    user.value = data.user
    token.value = data.token
    localStorage.setItem('user', JSON.stringify(data.user))
    localStorage.setItem('token', data.token)
  }

  async function logout() {
    try {
      await api.post('/logout')
    } catch (e) {
      // ignore errors on logout
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('user')
      localStorage.removeItem('token')
    }
  }

  async function fetchUser() {
    if (!token.value) return
    try {
      const { data } = await api.get('/user')
      user.value = data
      localStorage.setItem('user', JSON.stringify(data))
    } catch (e) {
      user.value = null
      token.value = null
      localStorage.removeItem('user')
      localStorage.removeItem('token')
    }
  }

  return { user, token, isAuthenticated, login, register, logout, fetchUser }
})
