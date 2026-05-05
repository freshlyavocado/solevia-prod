import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'
import type { Cart } from '../types'

export const useCartStore = defineStore('cart', () => {
  const cart = ref<Cart | null>(null)
  const loading = ref(false)

  async function fetchCart() {
    loading.value = true
    try {
      const { data } = await api.get('/cart')
      cart.value = data
    } catch (error) {
      console.error('Error fetching cart:', error)
    } finally {
      loading.value = false
    }
  }

  async function addItem(variantId: number, quantity: number) {
    loading.value = true
    try {
      const { data } = await api.post('/cart/items', { variant_id: variantId, quantity })
      cart.value = data
    } catch (error) {
      console.error('Error adding item to cart:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  async function updateItem(itemId: number, quantity: number) {
    loading.value = true
    try {
      const { data } = await api.put(`/cart/items/${itemId}`, { quantity })
      cart.value = data
    } catch (error) {
      console.error('Error updating cart item:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  async function removeItem(itemId: number) {
    loading.value = true
    try {
      const { data } = await api.delete(`/cart/items/${itemId}`)
      cart.value = data
    } catch (error) {
      console.error('Error removing item from cart:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  function clearCart() {
    cart.value = null
  }

  return { cart, loading, fetchCart, addItem, updateItem, removeItem, clearCart }
})
