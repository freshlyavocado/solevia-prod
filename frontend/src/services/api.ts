/**
 * INFO FILE
 * Nama: api.ts
 * Fungsi: Konfigurasi Axios untuk melakukan HTTP request (GET/POST) ke Laravel Backend, dilengkapi dengan penyisipan Token otomatis.
 */

import axios from 'axios'

// Membuat instance Axios custom dengan pengaturan bawaan (default)
const api = axios.create({
  // URL dasar API. Akan membaca dari .env (VITE_API_BASE_URL), jika tidak ada maka menggunakan domain produksi
  baseURL: import.meta.env.VITE_API_BASE_URL || 'https://api-solevia.athayaafatih.my.id/api',
  // Pengaturan header standar agar backend tahu kita mengirim dan meminta data berformat JSON
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// Request Interceptor: Fungsi ini akan selalu dijalankan SEBELUM setiap request dikirim ke backend
api.interceptors.request.use((config) => {
  // Mengambil token otentikasi dari memori browser (localStorage)
  const token = localStorage.getItem('token')
  // Jika token ada, otomatis selipkan ke dalam header 'Authorization'
  // Ini berguna agar kita tidak perlu memasukkan token manual setiap kali memanggil API yang butuh login
  if (token && config.headers) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Response Interceptor: Fungsi ini akan selalu dijalankan SETELAH mendapat respon dari backend
api.interceptors.response.use(
  // Jika respon sukses, kembalikan respon apa adanya
  (response) => response,
  // Jika respon gagal (error)
  (error) => {
    // Mengecek apakah error tersebut adalah 401 Unauthorized (Artinya token expired atau user belum login)
    if (error.response && error.response.status === 401) {
      // Hapus token dan data user yang sudah tidak valid dari browser
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      // Catatan: Proses mengarahkan pengguna kembali ke halaman Login bisa dilakukan di sini
    }
    // Lemparkan error tersebut agar bisa ditangkap oleh blok catch() di komponen
    return Promise.reject(error)
  }
)

/**
 * Helper function: Menghasilkan URL lengkap untuk mengakses file dari backend storage.
 * Digunakan untuk menampilkan gambar produk, logo brand, dll.
 * Contoh: storageUrl('products/image.jpg') → 'https://api-solevia.athayaafatih.my.id/storage/products/image.jpg'
 */
export const storageUrl = (path: string): string => {
  // Ambil base URL dari env variable, hapus '/api' di akhir untuk mendapat root URL backend
  const baseUrl = (import.meta.env.VITE_API_BASE_URL || 'https://api-solevia.athayaafatih.my.id/api').replace(/\/api\/?$/, '')
  return `${baseUrl}/storage/${path}`
}

export default api

