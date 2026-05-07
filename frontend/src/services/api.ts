/**
 * INFO FILE
 * Nama: api.ts
 * Fungsi: Konfigurasi Axios untuk melakukan HTTP request (GET/POST) ke Laravel Backend, dilengkapi dengan penyisipan Token otomatis.
 */

import axios from 'axios'

// Membuat instance Axios custom dengan pengaturan bawaan (default)
const api = axios.create({
  // URL dasar API. Akan membaca dari .env (VITE_API_URL), jika tidak ada maka menggunakan localhost:8000
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
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

export default api
