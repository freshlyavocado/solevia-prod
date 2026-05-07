/**
 * INFO FILE
 * Nama: index.ts (types)
 * Fungsi: Mendefinisikan tipe data (Interfaces) TypeScript untuk seluruh aplikasi.
 * Tipe data ini digunakan untuk memastikan bentuk objek selalu konsisten (mencegah typo).
 */

// Tipe data untuk Pengguna (User)
export interface User {
  id: number
  name: string
  email: string
  created_at?: string
  updated_at?: string
}

// Tipe data untuk Kategori
export interface Category {
  id: number
  name: string
  slug: string
  products_count?: number // Tanda tanya (?) berarti parameter ini opsional (boleh tidak ada)
}

// Tipe data untuk Merek (Brand)
export interface Brand {
  id: number
  name: string
  slug: string
  description?: string
  logo_url?: string
  products_count?: number
}

// Tipe data untuk Gambar Produk
export interface ProductImage {
  id: number
  image_url: string // Path/URL gambar di dalam server
}

// Tipe data untuk Varian Produk (Ukuran dan Warna)
export interface ProductVariant {
  id: number
  product_id: number
  size?: string // Ukuran sepatu
  color?: string
  stock: number // Jumlah stok untuk varian ini
  sku?: string
  product?: Product // Relasi balik ke produk induk
}

// Tipe data utama untuk Produk
export interface Product {
  id: number
  brand_id: number
  category_id: number
  name: string
  slug: string
  description?: string
  price: number // Harga asli
  discount_price?: number // Harga setelah diskon (jika ada)
  category?: Category // Relasi ke objek kategori
  brand?: Brand // Relasi ke objek brand
  images?: ProductImage[] // Array dari kumpulan gambar
  variants?: ProductVariant[] // Array dari ukuran yang tersedia
}

// Tipe data untuk Item di dalam Keranjang
export interface CartItem {
  id: number
  cart_id: number
  variant_id: number
  quantity: number // Jumlah yang ingin dibeli
  variant?: ProductVariant // Relasi ke varian spesifik
}

// Tipe data untuk Keranjang Belanja keseluruhan
export interface Cart {
  id: number
  user_id: number
  items?: CartItem[] // Daftar barang-barang yang ada di keranjang
}
