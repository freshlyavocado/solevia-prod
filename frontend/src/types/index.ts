export interface User {
  id: number
  name: string
  email: string
  created_at?: string
  updated_at?: string
}

export interface Category {
  id: number
  name: string
  slug: string
  products_count?: number
}

export interface Brand {
  id: number
  name: string
  slug: string
  description?: string
  logo_url?: string
  products_count?: number
}

export interface ProductImage {
  id: number
  image_url: string
}

export interface ProductVariant {
  id: number
  product_id: number
  size?: string
  color?: string
  stock: number
  sku?: string
  product?: Product // loaded dynamically via relations
}

export interface Product {
  id: number
  name: string
  slug: string
  description: string
  price: number
  discount_price?: number
  category_id: number
  brand_id: number
  category?: Category
  brand?: Brand
  images?: ProductImage[]
  variants?: ProductVariant[]
}

export interface CartItem {
  id: number
  cart_id: number
  variant_id: number
  quantity: number
  variant: ProductVariant
}

export interface Cart {
  id: number
  user_id: number
  items: CartItem[]
}

export interface Order {
  id: number
  user_id: number
  order_number: string
  total_amount: number
  status: string
  payment_status: string
  created_at: string
}
