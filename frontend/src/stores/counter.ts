/**
 * INFO FILE
 * Nama: counter.ts
 * Fungsi: Store bawaan dari template Vue (contoh bawaan Pinia). Tidak digunakan di aplikasi utama Solevia.
 */

import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useCounterStore = defineStore('counter', () => {
  // State: menyimpan angka
  const count = ref(0)
  
  // Getter: mengembalikan hasil perhitungan count * 2
  const doubleCount = computed(() => count.value * 2)
  
  // Action: menambahkan angka
  function increment() {
    count.value++
  }

  return { count, doubleCount, increment }
})
