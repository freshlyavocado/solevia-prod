/**
 * INFO FILE
 * Nama: cart.ts
 * Fungsi: Pinia Store untuk mengelola fungsi keranjang belanja pengguna.
 */

import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'
import type { Cart } from '../types'

export const useCartStore = defineStore('cart', () => {
  // --- STATE ---
  // Menyimpan data keranjang belanja saat ini
  const cart = ref<Cart | null>(null)
  // Menyimpan status loading saat memproses API keranjang
  const loading = ref(false)

  // --- ACTIONS ---

  // Mengambil data keranjang milik user yang sedang login dari backend
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

  // Menambahkan produk (dengan ID varian tertentu) ke dalam keranjang
  async function addItem(variantId: number, quantity: number) {
    loading.value = true
    try {
      // POST ke endpoint /cart/items membawa variant_id dan jumlah barang
      const { data } = await api.post('/cart/items', { variant_id: variantId, quantity })
      cart.value = data // Memperbarui data keranjang dengan keranjang terbaru dari server
    } catch (error) {
      console.error('Error adding item to cart:', error)
      throw error // Melemparkan error ke komponen agar bisa ditangani (misal: muncul popup error)
    } finally {
      loading.value = false
    }
  }

  // Memperbarui jumlah barang pada satu item yang sudah ada di keranjang
  async function updateItem(itemId: number, quantity: number) {
    loading.value = true
    try {
      // PUT ke endpoint /cart/items/{itemId}
      const { data } = await api.put(`/cart/items/${itemId}`, { quantity })
      cart.value = data
    } catch (error) {
      console.error('Error updating cart item:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  // Menghapus produk dari dalam keranjang
  async function removeItem(itemId: number) {
    loading.value = true
    try {
      // DELETE ke endpoint /cart/items/{itemId}
      const { data } = await api.delete(`/cart/items/${itemId}`)
      cart.value = data
    } catch (error) {
      console.error('Error removing item from cart:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  // Fungsi untuk mengosongkan keranjang pada tampilan (misal, setelah logout atau berhasil checkout)
  function clearCart() {
    cart.value = null
  }

  return { cart, loading, fetchCart, addItem, updateItem, removeItem, clearCart }
})
