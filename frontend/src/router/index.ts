import { createRouter, createWebHistory } from 'vue-router'
import DefaultLayout from '../layouts/DefaultLayout.vue'
import AuthLayout from '../layouts/AuthLayout.vue'
import HomeView from '../pages/HomeView.vue'
import BrandsView from '../pages/BrandsView.vue'
import BrandDetailView from '../pages/BrandDetailView.vue'
import ProductDetailView from '../pages/ProductDetailView.vue'
import DealsView from '../pages/DealsView.vue'
import LoginView from '../pages/auth/LoginView.vue'
import SignupView from '../pages/auth/SignupView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component: DefaultLayout,
      children: [
        {
          path: '',
          name: 'home',
          component: HomeView
        },
        {
          path: 'deals',
          name: 'deals',
          component: DealsView
        },
        {
          path: 'men',
          name: 'men',
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
          path: 'brands',
          name: 'brands',
          component: BrandsView
        },
        {
          path: 'brands/:id',
          name: 'brand-detail',
          component: BrandDetailView
        },
        {
          path: 'product/:slug',
          name: 'product-detail',
          component: ProductDetailView
        }
      ]
    },
    {
      path: '/',
      component: AuthLayout,
      children: [
        {
          path: 'login',
          name: 'login',
          component: LoginView
        },
        {
          path: 'signup',
          name: 'signup',
          component: SignupView
        }
      ]
    }
  ],
  scrollBehavior() {
    return { top: 0 }
  }
})

export default router
