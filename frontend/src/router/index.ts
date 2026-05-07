/**
 * INFO FILE
 * Nama: index.ts
 * Fungsi: Konfigurasi Vue Router (SPA) untuk memetakan alamat URL ke komponen halaman (Views) yang sesuai.
 */

// Mengimpor fungsi-fungsi utama dari vue-router
import { createRouter, createWebHistory } from 'vue-router'

// Mengimpor Layouts (Kerangka Halaman)
import DefaultLayout from '../layouts/DefaultLayout.vue'
import AuthLayout from '../layouts/AuthLayout.vue'

// Mengimpor komponen-komponen Halaman Utama
import HomeView from '../pages/HomeView.vue'
import BrandsView from '../pages/BrandsView.vue'
import BrandDetailView from '../pages/BrandDetailView.vue'
import ProductDetailView from '../pages/ProductDetailView.vue'
import DealsView from '../pages/DealsView.vue'
import CategoryView from '../pages/CategoryView.vue'  
import SearchView from '../pages/SearchView.vue'
import AboutUsView from '../pages/AboutUsView.vue'
import ContactUsView from '../pages/ContactUsView.vue'
import SizeGuideView from '../pages/SizeGuideView.vue'
import CartView from '../pages/CartView.vue'
import WishlistView from '../pages/WishlistView.vue'
import ProfileView from '../pages/ProfileView.vue'

// Mengimpor komponen-komponen Otentikasi
import LoginView from '../pages/auth/LoginView.vue'
import SignupView from '../pages/auth/SignupView.vue'

// Membuat instance router
const router = createRouter({
  // Menggunakan history HTML5 agar URL terlihat bersih tanpa tanda pagar (/#/)
  history: createWebHistory(import.meta.env.BASE_URL),
  
  // Mendaftarkan semua rute (peta jalan) aplikasi
  routes: [
    {
      // --- GRUP DEFAULT LAYOUT ---
      // Semua rute di bawah ini akan memiliki Navbar di atas dan Footer di bawah
      path: '/',
      component: DefaultLayout,
      children: [
        {
          path: '', // Rute untuk Beranda (http://localhost:5173/)
          name: 'home',
          component: HomeView
        },
        {
          path: 'deals', // Rute untuk halaman diskon (/deals)
          name: 'deals',
          component: DealsView
        },
        {
          path: 'men',
          name: 'men',
          // Lazy Loading: Komponen baru diunduh browser saat link diklik (meningkatkan performa)
          component: () => import('../pages/CategoryView.vue')
        },
        {
          path: 'women',
          name: 'women',
          component: () => import('../pages/CategoryView.vue')
        },
        {
          path: 'kids',
          name: 'kids',
          component: () => import('../pages/CategoryView.vue')
        },
        {
          path: 'search',
          name: 'search',
          component: SearchView
        },
        {
          path: 'about',
          name: 'about',
          component: AboutUsView
        },
        {
          path: 'contact',
          name: 'contact',
          component: ContactUsView
        },
        {
          path: 'size-guide',
          name: 'size-guide',
          component: SizeGuideView
        },
        {
          path: 'cart',
          name: 'cart',
          component: CartView
        },
        {
          path: 'wishlist',
          name: 'wishlist',
          component: WishlistView
        },
        {
          path: 'profile',
          name: 'profile',
          component: ProfileView
        },
        {
          path: 'brands',
          name: 'brands',
          component: BrandsView
        },
        {
          path: 'brands/:id', // URL dinamis untuk setiap brand (contoh: /brands/1)
          name: 'brand-detail',
          component: BrandDetailView
        },
        {
          path: 'product/:slug', // URL dinamis berdasarkan slug produk (contoh: /product/nike-air-max)
          name: 'product-detail',
          component: ProductDetailView
        }
      ]
    },
    {
      // --- GRUP AUTH LAYOUT ---
      // Rute di bawah ini TIDAK menampilkan Navbar/Footer utuh
      path: '/',
      component: AuthLayout,
      children: [
        {
          path: 'login', // (/login)
          name: 'login',
          component: LoginView
        },
        {
          path: 'signup', // (/signup)
          name: 'signup',
          component: SignupView
        }
      ]
    }
  ],
  
  // Fungsi otomatis: Setiap kali pindah halaman, kembalikan posisi scroll ke paling atas (top: 0)
  scrollBehavior() {
    return { top: 0 }
  }
})

// Mengekspor router agar bisa dipasang (mount) ke aplikasi utama di main.ts
export default router
