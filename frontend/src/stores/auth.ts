/**
 * INFO FILE
 * Nama: auth.ts
 * Fungsi: Pinia Store untuk mengelola state otentikasi pengguna (token login, data user, dan fungsi logout).
 */

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'
import type { User } from '../types'

// Mendefinisikan store bernama 'auth'
export const useAuthStore = defineStore('auth', () => {
  // --- STATE (Data) ---
  // Menyimpan data user yang sedang login. Mengambil dari localStorage jika sebelumnya sudah login.
  const user = ref<User | null>(JSON.parse(localStorage.getItem('user') || 'null'))
  // Menyimpan token akses dari backend.
  const token = ref<string | null>(localStorage.getItem('token') || null)

  // --- GETTERS (Data Turunan) ---
  // Fungsi computed untuk mengecek apakah user sudah login atau belum secara instan
  const isAuthenticated = computed(() => !!token.value)

  // --- ACTIONS (Fungsi untuk mengubah data) ---
  
  // Fungsi Login: Mengirim email & password ke backend
  async function login(credentials: Record<string, any>) {
    const { data } = await api.post('/login', credentials) // Memanggil endpoint login
    user.value = data.user // Menyimpan data user ke state Pinia
    token.value = data.token // Menyimpan token ke state Pinia
    // Menyimpan juga ke localStorage agar sesi tidak hilang saat halaman di-refresh
    localStorage.setItem('user', JSON.stringify(data.user))
    localStorage.setItem('token', data.token)
  }

  // Fungsi Register: Mendaftarkan user baru ke backend
  async function register(payload: Record<string, any>) {
    const { data } = await api.post('/register', payload)
    user.value = data.user
    token.value = data.token
    localStorage.setItem('user', JSON.stringify(data.user))
    localStorage.setItem('token', data.token)
  }

  // Fungsi Logout: Menghapus sesi dari backend dan memori lokal
  async function logout() {
    try {
      await api.post('/logout') // Memberitahu backend untuk menonaktifkan token
    } catch (e) {
      // Mengabaikan error jika misalnya token sudah kadaluarsa di backend
    } finally {
      // Mengosongkan data di state Pinia
      user.value = null
      token.value = null
      // Menghapus data dari penyimpanan browser
      localStorage.removeItem('user')
      localStorage.removeItem('token')
    }
  }

  // Fungsi Fetch User: Mengambil data profil terbaru user menggunakan token yang ada
  async function fetchUser() {
    if (!token.value) return // Jika tidak ada token, batalkan proses
    try {
      const { data } = await api.get('/user')
      user.value = data
      localStorage.setItem('user', JSON.stringify(data))
    } catch (e) {
      // Jika token ditolak (misal sudah expired), bersihkan data
      user.value = null
      token.value = null
      localStorage.removeItem('user')
      localStorage.removeItem('token')
    }
  }

  // Mengembalikan semua state dan fungsi agar bisa digunakan oleh komponen (seperti Navbar atau LoginView)
  return { user, token, isAuthenticated, login, register, logout, fetchUser }
})
