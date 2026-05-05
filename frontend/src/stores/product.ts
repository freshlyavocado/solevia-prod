import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'
import type { Product, Category, Brand } from '../types'

export const useProductStore = defineStore('product', () => {
  const products = ref<Product[]>([])
  const categories = ref<Category[]>([])
  const brands = ref<Brand[]>([])
  const loading = ref(false)

  async function fetchProducts(params: Record<string, any> = {}) {
    loading.value = true
    try {
      const { data } = await api.get('/products', { params })
      products.value = data.data || data // Handles pagination if structure varies
    } catch (error) {
      console.error('Error fetching products:', error)
    } finally {
      loading.value = false
    }
  }

  async function fetchCategories() {
    try {
      const { data } = await api.get('/categories')
      categories.value = data
    } catch (error) {
      console.error('Error fetching categories:', error)
    }
  }

  async function fetchBrands() {
    try {
      const { data } = await api.get('/brands')
      brands.value = data
    } catch (error) {
      console.error('Error fetching brands:', error)
    }
  }

  return { products, categories, brands, loading, fetchProducts, fetchCategories, fetchBrands }
})
