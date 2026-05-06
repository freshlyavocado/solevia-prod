# 📬 Full API Guide & Postman Testing — Solevia Backend

## Setup Awal Postman

### 1. Buat Collection Baru
- Buka Postman → **New Collection** → Nama: `Solevia API`

### 2. Set Base URL sebagai Variable
- Klik collection → tab **Variables**
- Tambah variable:

| Variable | Initial Value |
|----------|--------------|
| `base_url` | `http://localhost:8000/api` |
| `token` | *(kosong, diisi otomatis setelah login)* |

### 3. Set Headers Default
- Klik collection → tab **Headers**, tambah:

| Key | Value |
|-----|-------|
| `Accept` | `application/json` |
| `Content-Type` | `application/json` |

### 4. Set Authorization Default (untuk protected routes)
- Klik collection → tab **Authorization**
- Type: **Bearer Token**
- Token: `{{token}}`

> Semua request dalam collection akan otomatis pakai token ini kecuali di-override.

---

## Urutan Testing (Ikuti Sesuai Nomor!)

```
1. Register → 2. Login → simpan token
3. Products (public) → 4. Categories → 5. Brands
6. Add to Cart → 7. View Cart → 8. Update Cart → 9. Remove from Cart
10. Add to Cart lagi → 11. Checkout
12. View Orders → 13. Order Detail → 14. Confirm Payment
15. Wishlist Add → 16. Wishlist List → 17. Wishlist Remove
18. Get User → 19. Logout
```

---

## 🔓 A. PUBLIC ROUTES (Tanpa Token)

---

### 1. Register

Membuat akun baru dan mendapatkan token.

```
POST {{base_url}}/register
```

**Body (raw JSON):**
```json
{
    "name": "Athaya Fatih",
    "email": "athaya@test.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201 Created):**
```json
{
    "user": {
        "id": 1,
        "name": "Athaya Fatih",
        "email": "athaya@test.com",
        "updated_at": "2026-05-06T03:00:00.000000Z",
        "created_at": "2026-05-06T03:00:00.000000Z"
    },
    "token": "1|abc123xyz..."
}
```

**Auto-save token:** Tambahkan di tab **Scripts → Post-response**:
```javascript
var jsonData = pm.response.json();
pm.collectionVariables.set("token", jsonData.token);
```

---

### 2. Login

Login dan mendapatkan token baru.

```
POST {{base_url}}/login
```

**Body (raw JSON):**
```json
{
    "email": "athaya@test.com",
    "password": "password123"
}
```

**Response (200 OK):**
```json
{
    "user": {
        "id": 1,
        "name": "Athaya Fatih",
        "email": "athaya@test.com"
    },
    "token": "2|def456uvw..."
}
```

**Auto-save token:** Sama seperti register, tambahkan script:
```javascript
var jsonData = pm.response.json();
pm.collectionVariables.set("token", jsonData.token);
```

**Error test — email salah:**
```json
{
    "email": "wrong@test.com",
    "password": "password123"
}
```
→ Response: `422 Unprocessable Entity` dengan pesan error.

---

### 3. List Products

Ambil semua produk dengan pagination.

```
GET {{base_url}}/products
```

**Response (200 OK):**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "name": "Air Max 90",
            "slug": "air-max-90",
            "description": "Classic Nike Air Max 90...",
            "price": "1500000.00",
            "discount_price": null,
            "category_id": 1,
            "brand_id": 1,
            "category": { "id": 1, "name": "Sneakers" },
            "brand": { "id": 1, "name": "Nike" },
            "images": [{ "id": 1, "image_url": "products/placeholder.png" }],
            "variants": [
                { "id": 1, "size": "38", "stock": 25 },
                { "id": 2, "size": "39", "stock": 40 }
            ]
        }
    ],
    "per_page": 12,
    "total": 8,
    "last_page": 1
}
```

**Variasi testing dengan Query Parameters:**

| Test | URL |
|------|-----|
| Filter by category | `{{base_url}}/products?category_id=1` |
| Filter by brand | `{{base_url}}/products?brand_id=2` |
| Search by name | `{{base_url}}/products?search=air` |
| Custom pagination | `{{base_url}}/products?per_page=4&page=2` |
| Gabungan filter | `{{base_url}}/products?category_id=1&brand_id=1&search=air` |

---

### 4. Product Detail

Ambil detail 1 produk berdasarkan slug.

```
GET {{base_url}}/products/air-max-90
```

**Response (200 OK):**
```json
{
    "id": 1,
    "name": "Air Max 90",
    "slug": "air-max-90",
    "description": "Classic Nike Air Max 90 with visible Air cushioning.",
    "price": "1500000.00",
    "category": { "id": 1, "name": "Sneakers" },
    "brand": { "id": 1, "name": "Nike" },
    "images": [{ "id": 1, "image_url": "products/placeholder.png" }],
    "variants": [
        { "id": 1, "size": "38", "stock": 25 },
        { "id": 2, "size": "39", "stock": 40 },
        { "id": 3, "size": "40", "stock": 15 }
    ]
}
```

**Error test — slug tidak ada:**
```
GET {{base_url}}/products/produk-tidak-ada
```
→ Response: `404 Not Found`

---

### 5. List Categories

```
GET {{base_url}}/categories
```

**Response (200 OK):**
```json
[
    { "id": 1, "name": "Sneakers", "description": "Casual and sporty sneakers" },
    { "id": 2, "name": "Running", "description": "Performance running shoes" },
    { "id": 3, "name": "Formal", "description": "Elegant formal shoes" },
    { "id": 4, "name": "Sandals", "description": "Comfortable sandals" }
]
```

---

### 6. List Brands

```
GET {{base_url}}/brands
```

**Response (200 OK):**
```json
[
    { "id": 1, "name": "Nike", "description": "Just Do It" },
    { "id": 2, "name": "Adidas", "description": "Impossible Is Nothing" },
    { "id": 3, "name": "Puma", "description": "Forever Faster" },
    { "id": 4, "name": "New Balance", "description": "Fearlessly Independent" }
]
```

---

### 7. Brand Detail

```
GET {{base_url}}/brands/1
```

**Response (200 OK):**
```json
{
    "id": 1,
    "name": "Nike",
    "description": "Just Do It"
}
```

---

## 🔒 B. PROTECTED ROUTES (Butuh Token)

> **PENTING:** Pastikan sudah login dan token tersimpan di variable `{{token}}`.
> Setiap request di bawah ini harus memiliki header:
> `Authorization: Bearer {{token}}`

---

### 8. Get Current User

Ambil data user yang sedang login.

```
GET {{base_url}}/user
```

**Response (200 OK):**
```json
{
    "id": 1,
    "name": "Athaya Fatih",
    "email": "athaya@test.com",
    "email_verified_at": null,
    "created_at": "2026-05-06T03:00:00.000000Z",
    "updated_at": "2026-05-06T03:00:00.000000Z"
}
```

**Error test — tanpa token:**
Hapus header Authorization → Response: `401 Unauthorized`

---

### 9. Add to Cart

Tambah produk ke keranjang. Butuh `variant_id` (dari product detail).

```
POST {{base_url}}/cart/items
```

**Body (raw JSON):**
```json
{
    "variant_id": 1,
    "quantity": 2
}
```

> `variant_id: 1` = Air Max 90, size 38

**Response (200 OK):**
```json
{
    "id": 1,
    "user_id": 1,
    "items": [
        {
            "id": 1,
            "cart_id": 1,
            "variant_id": 1,
            "quantity": 2,
            "variant": {
                "id": 1,
                "size": "38",
                "stock": 25,
                "product": {
                    "id": 1,
                    "name": "Air Max 90",
                    "price": "1500000.00",
                    "images": [{ "image_url": "products/placeholder.png" }]
                }
            }
        }
    ]
}
```

**Test tambah item kedua (produk berbeda):**
```json
{
    "variant_id": 8,
    "quantity": 1
}
```

**Test tambah item yang sudah ada (quantity akan bertambah):**
```json
{
    "variant_id": 1,
    "quantity": 1
}
```
→ Quantity item pertama akan menjadi 3.

**Error test — variant_id tidak ada:**
```json
{
    "variant_id": 9999,
    "quantity": 1
}
```
→ Response: `422` — "The selected variant_id is invalid."

---

### 10. View Cart

Lihat isi keranjang lengkap.

```
GET {{base_url}}/cart
```

**Response (200 OK):** Sama seperti response add to cart di atas.

---

### 11. Update Cart Item Quantity

Ubah jumlah item di keranjang. `{id}` = cart_item id (bukan variant_id).

```
PUT {{base_url}}/cart/items/1
```

**Body (raw JSON):**
```json
{
    "quantity": 5
}
```

**Response (200 OK):** Cart dengan quantity item #1 diubah menjadi 5.

**Error test — quantity 0:**
```json
{
    "quantity": 0
}
```
→ Response: `422` — "The quantity field must be at least 1."

---

### 12. Remove Cart Item

Hapus item dari keranjang.

```
DELETE {{base_url}}/cart/items/1
```

**Response (200 OK):** Cart tanpa item yang dihapus.

---

### 13. Checkout (Buat Order)

Buat pesanan dari isi keranjang. **Pastikan cart ada isinya!** (tambah item dulu kalau sudah kosong).

```
POST {{base_url}}/checkout
```

**Body (raw JSON):**
```json
{
    "recipient_name": "Athaya Fatih",
    "phone_number": "081234567890",
    "address": "Jl. Merdeka No. 10, RT 01/RW 02",
    "city": "Jakarta Selatan",
    "province": "DKI Jakarta",
    "postal_code": "12345",
    "payment_method": "qris"
}
```

> `payment_method` hanya boleh: `"qris"` atau `"cod"`

**Response (201 Created):**
```json
{
    "id": 1,
    "user_id": 1,
    "order_number": "ORD-A1B2C3D4",
    "total_amount": "3000000.00",
    "status": "pending",
    "payment_status": "unpaid",
    "created_at": "2026-05-06T03:15:00.000000Z",
    "items": [
        {
            "id": 1,
            "order_id": 1,
            "variant_id": 1,
            "quantity": 2,
            "item_price": "1500000.00",
            "variant": {
                "product": { "name": "Air Max 90" }
            }
        }
    ],
    "payment": {
        "id": 1,
        "payment_method": "qris",
        "amount": "3000000.00",
        "status": "pending",
        "paid_at": null
    },
    "shipping": {
        "id": 1,
        "recipient_name": "Athaya Fatih",
        "phone_number": "081234567890",
        "address": "Jl. Merdeka No. 10, RT 01/RW 02",
        "city": "Jakarta Selatan",
        "province": "DKI Jakarta",
        "postal_code": "12345",
        "shipping_cost": "0.00"
    }
}
```

> **Simpan `id` order** (misal: 1) untuk test selanjutnya.

**Error test — cart kosong (checkout lagi tanpa tambah item):**
→ Response: `422` — "Cart is empty"

**Error test — payment_method salah:**
```json
{ "payment_method": "transfer" }
```
→ Response: `422` — "The selected payment method is invalid."

---

### 14. List Orders

Lihat semua pesanan milik user yang login.

```
GET {{base_url}}/orders
```

**Response (200 OK):**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "order_number": "ORD-A1B2C3D4",
            "total_amount": "3000000.00",
            "status": "pending",
            "payment_status": "unpaid",
            "items": [...],
            "payment": {...},
            "shipping": {...}
        }
    ],
    "per_page": 10,
    "total": 1
}
```

---

### 15. Order Detail

Lihat detail 1 pesanan. Ganti `1` dengan order id.

```
GET {{base_url}}/orders/1
```

**Response (200 OK):** Detail lengkap order dengan items, payment, dan shipping.

**Error test — order milik user lain:**
```
GET {{base_url}}/orders/999
```
→ Response: `404 Not Found`

---

### 16. Confirm Payment

Konfirmasi pembayaran (ubah status menjadi "paid").

```
POST {{base_url}}/orders/1/confirm-payment
```

**Body:** Tidak perlu body.

**Response (200 OK):**
```json
{
    "id": 1,
    "order_number": "ORD-A1B2C3D4",
    "status": "paid",
    "payment_status": "paid",
    "payment": {
        "status": "paid",
        "paid_at": "2026-05-06T03:20:00.000000Z"
    }
}
```

**Error test — confirm lagi yang sudah paid:**
```
POST {{base_url}}/orders/1/confirm-payment
```
→ Response: `422` — "Already paid"

---

### 17. Add to Wishlist

Tambah produk ke wishlist.

```
POST {{base_url}}/wishlists
```

**Body (raw JSON):**
```json
{
    "product_id": 1
}
```

**Response (201 Created):**
```json
{
    "id": 1,
    "user_id": 1,
    "product_id": 1,
    "created_at": "2026-05-06T03:25:00.000000Z"
}
```

**Test — tambah produk yang sama (tidak duplikat, pakai firstOrCreate):**
```json
{ "product_id": 1 }
```
→ Response: `201` — return wishlist yang sudah ada.

---

### 18. List Wishlists

```
GET {{base_url}}/wishlists
```

**Response (200 OK):**
```json
[
    {
        "id": 1,
        "user_id": 1,
        "product_id": 1,
        "product": {
            "id": 1,
            "name": "Air Max 90",
            "price": "1500000.00",
            "images": [{ "image_url": "products/placeholder.png" }],
            "category": { "name": "Sneakers" },
            "brand": { "name": "Nike" }
        }
    }
]
```

---

### 19. Remove from Wishlist

Hapus dari wishlist. `{id}` = wishlist id (bukan product_id).

```
DELETE {{base_url}}/wishlists/1
```

**Response (200 OK):**
```json
{
    "message": "Removed from wishlist"
}
```

---

### 20. Logout

Hapus token yang sedang dipakai (revoke).

```
POST {{base_url}}/logout
```

**Body:** Tidak perlu body.

**Response (200 OK):**
```json
{
    "message": "Logged out"
}
```

> Setelah logout, token di variable `{{token}}` sudah tidak valid. Request berikutnya dengan token ini akan mendapat `401 Unauthorized`. Harus login ulang.

---

## 📋 Cheat Sheet — Semua Endpoints

| # | Method | Endpoint | Auth | Fungsi |
|---|--------|----------|------|--------|
| 1 | POST | `/register` | ❌ | Daftar akun baru |
| 2 | POST | `/login` | ❌ | Login, dapat token |
| 3 | GET | `/products` | ❌ | List produk + filter |
| 4 | GET | `/products/{slug}` | ❌ | Detail produk |
| 5 | GET | `/categories` | ❌ | List kategori |
| 6 | GET | `/brands` | ❌ | List brand |
| 7 | GET | `/brands/{id}` | ❌ | Detail brand |
| 8 | GET | `/user` | ✅ | Data user login |
| 9 | POST | `/cart/items` | ✅ | Tambah ke cart |
| 10 | GET | `/cart` | ✅ | Lihat cart |
| 11 | PUT | `/cart/items/{id}` | ✅ | Update quantity |
| 12 | DELETE | `/cart/items/{id}` | ✅ | Hapus dari cart |
| 13 | POST | `/checkout` | ✅ | Buat order |
| 14 | GET | `/orders` | ✅ | List order |
| 15 | GET | `/orders/{id}` | ✅ | Detail order |
| 16 | POST | `/orders/{id}/confirm-payment` | ✅ | Bayar order |
| 17 | POST | `/wishlists` | ✅ | Tambah wishlist |
| 18 | GET | `/wishlists` | ✅ | List wishlist |
| 19 | DELETE | `/wishlists/{id}` | ✅ | Hapus wishlist |
| 20 | POST | `/logout` | ✅ | Logout |

---

## ⚠️ Common Errors

| Status Code | Penyebab | Solusi |
|-------------|----------|-------|
| `401 Unauthorized` | Token tidak ada / expired / sudah logout | Login ulang, copy token baru |
| `404 Not Found` | URL salah / data tidak ditemukan | Cek slug/id yang dipakai |
| `405 Method Not Allowed` | HTTP method salah (GET vs POST) | Cek tabel di atas |
| `422 Unprocessable Entity` | Validasi gagal | Baca pesan error, perbaiki body |
| `500 Internal Server Error` | Bug di server | Cek terminal `php artisan serve` |

## 💡 Tips Postman

1. **Gunakan script auto-save token** di request Login & Register agar tidak perlu copy-paste manual
2. **Buat folder** di collection: `Auth`, `Products`, `Cart`, `Orders`, `Wishlist`
3. **Gunakan Environment** jika punya URL berbeda (local vs production)
4. **Cek tab Console** di bawah Postman untuk debug request/response mentah
