/**
 * INFO FILE
 * Nama: product.ts
 * Fungsi: Pinia Store sentral untuk menyimpan dan mengambil data produk, kategori, dan brand dari Backend API.
 */

import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'
import type { Product, Category, Brand } from '../types'

export const useProductStore = defineStore('product', () => {
  // --- STATE (Data) ---
  // Menyimpan daftar semua produk (Sepatu, Pakaian, dll)
  const products = ref<Product[]>([])
  // Menyimpan daftar semua kategori (Men, Women, Kids)
  const categories = ref<Category[]>([])
  // Menyimpan daftar semua merek (Nike, Adidas, dll)
  const brands = ref<Brand[]>([])
  // Menyimpan status loading (apakah sedang meminta data ke server atau tidak)
  const loading = ref(false)

  // --- ACTIONS (Fungsi untuk berinteraksi dengan API) ---

  // Fungsi untuk mengambil semua produk dari database
  async function fetchProducts(params: Record<string, any> = {}) {
    loading.value = true // Menyalakan indikator loading
    try {
      const { data } = await api.get('/products', { params }) // Memanggil API /products dengan parameter (contoh: search, filter)
      products.value = data.data || data // Menyimpan hasil data ke dalam state products
    } catch (error) {
      console.error('Error fetching products:', error) // Menampilkan error di console browser jika gagal
    } finally {
      loading.value = false // Mematikan indikator loading
    }
  }

  // Fungsi untuk mengambil semua kategori dari database
  async function fetchCategories() {
    try {
      const { data } = await api.get('/categories') // Memanggil API /categories
      categories.value = data // Menyimpan daftar kategori ke state
    } catch (error) {
      console.error('Error fetching categories:', error)
    }
  }

  // Fungsi untuk mengambil semua merek/brand dari database
  async function fetchBrands() {
    try {
      const { data } = await api.get('/brands') // Memanggil API /brands
      brands.value = data // Menyimpan daftar brand ke state
    } catch (error) {
      console.error('Error fetching brands:', error)
    }
  }

  // Mengekspor state dan fungsi-fungsi di atas agar bisa diakses oleh komponen Vue manapun
  return { products, categories, brands, loading, fetchProducts, fetchCategories, fetchBrands }
})
