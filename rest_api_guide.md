# Panduan REST API Solevia — Untuk Pemula

---

## 1. Apa Itu REST API?

Bayangkan kamu punya **2 aplikasi terpisah**:
- **Backend** (Laravel) → menyimpan data di database
- **Frontend** (Vue.js) → menampilkan data ke user

Mereka **tidak bisa langsung ngobrol**. REST API adalah **jembatan** di antaranya.

```
┌──────────┐     HTTP Request      ┌──────────┐
│          │  ──────────────────►   │          │
│ Frontend │   GET /api/products   │ Backend  │
│ (Vue.js) │                       │ (Laravel)│
│          │  ◄──────────────────  │          │
└──────────┘     JSON Response     └──────────┘
                 [{name: "Air Max"}, ...]
```

**Alur sederhana:**
1. Frontend kirim **request** → "Minta data produk dong!"
2. Backend terima, ambil dari database
3. Backend kirim **response** berupa **JSON** → data produk

---

## 2. Konsep Penting

### HTTP Method (Aksi yang dilakukan)

| Method | Fungsi | Contoh |
|--------|--------|--------|
| `GET` | **Ambil** data | Lihat daftar produk |
| `POST` | **Buat** data baru | Register akun, tambah ke cart |
| `PUT` | **Update** data | Ubah jumlah item di cart |
| `DELETE` | **Hapus** data | Hapus item dari cart |

### Endpoint (Alamat tujuan)

Endpoint = URL yang dituju. Contoh:
```
GET    /api/products          → ambil semua produk
GET    /api/products/air-max  → ambil 1 produk by slug
POST   /api/register          → daftar akun baru
POST   /api/cart/items        → tambah item ke cart
DELETE /api/cart/items/5       → hapus cart item id 5
```

### JSON (Format data)

Request dan response menggunakan format **JSON**:
```json
{
  "name": "Air Max 90",
  "price": 1500000,
  "category": "Sneakers"
}
```

### Status Code (Kode balasan)

| Code | Arti |
|------|------|
| `200` | OK — berhasil |
| `201` | Created — data berhasil dibuat |
| `401` | Unauthorized — belum login |
| `404` | Not Found — data tidak ditemukan |
| `422` | Validation Error — input salah |
| `500` | Server Error — ada bug di backend |

---

## 3. Cara Kerja di Laravel

### Struktur file:
```
app/Http/Controllers/Api/
├── AuthController.php        ← login, register, logout
├── ProductController.php     ← list & detail produk
├── CategoryController.php    ← list kategori
├── BrandController.php       ← list brand
├── CartController.php        ← kelola keranjang
├── WishlistController.php    ← kelola wishlist
├── CheckoutController.php    ← proses checkout
└── OrderController.php       ← lihat pesanan

routes/
└── api.php                   ← daftar semua endpoint
```

**Alur:** Request masuk → `routes/api.php` → Controller → Response

---

## 4. Membuat Controller

### Langkah:
```bash
php artisan make:controller Api/AuthController
php artisan make:controller Api/ProductController
php artisan make:controller Api/CategoryController
php artisan make:controller Api/BrandController
php artisan make:controller Api/CartController
php artisan make:controller Api/WishlistController
php artisan make:controller Api/CheckoutController
php artisan make:controller Api/OrderController
```

---

## 5. Kode Lengkap Setiap Controller

### 5.1 AuthController — Login, Register, Logout

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * REGISTER — Daftar akun baru
     * POST /api/register
     * Body: { name, email, password, password_confirmation }
     */
    public function register(Request $request): JsonResponse
    {
        // 1. Validasi input
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // 'confirmed' artinya harus ada field password_confirmation
        ]);

        // 2. Buat user baru di database
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 3. Buat token untuk user (agar langsung login)
        $token = $user->createToken('auth-token')->plainTextToken;

        // 4. Kirim response
        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201); // 201 = Created
    }

    /**
     * LOGIN — Masuk ke akun
     * POST /api/login
     * Body: { email, password }
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Cek email + password
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    /**
     * LOGOUT — Keluar dari akun
     * POST /api/logout (perlu token)
     */
    public function logout(Request $request): JsonResponse
    {
        // Hapus token yang sedang dipakai
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil logout']);
    }

    /**
     * GET USER — Ambil data user yang sedang login
     * GET /api/user (perlu token)
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
```

> [!NOTE]
> **Token** = kunci akses. Setelah login/register, frontend menyimpan token ini.
> Setiap request yang butuh login, frontend kirim token di header:
> `Authorization: Bearer <token>`

---

### 5.2 ProductController — Lihat Produk

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * LIST — Ambil semua produk (dengan filter & search)
     * GET /api/products
     * GET /api/products?category_id=1
     * GET /api/products?search=nike
     */
    public function index(Request $request): JsonResponse
    {
        // Mulai query, sertakan relasi (eager loading)
        $query = Product::with(['category', 'brand', 'images', 'variants']);

        // Filter by kategori (opsional)
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by brand (opsional)
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Search by nama (opsional)
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Paginate (12 produk per halaman)
        $products = $query->latest()->paginate(12);

        return response()->json($products);
    }

    /**
     * DETAIL — Ambil 1 produk berdasarkan slug
     * GET /api/products/air-max-90
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::with(['category', 'brand', 'images', 'variants'])
            ->where('slug', $slug)
            ->firstOrFail(); // 404 kalau tidak ditemukan

        return response()->json($product);
    }
}
```

> [!TIP]
> **`with(['category', 'brand', ...])`** = Eager Loading.
> Tanpa ini, setiap produk akan query database terpisah untuk category & brand (N+1 problem). Dengan `with()`, semua diambil sekaligus → lebih cepat.

---

### 5.3 CategoryController & BrandController

```php
<?php
// app/Http/Controllers/Api/CategoryController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     * Response: [{ id: 1, name: "Sneakers", products_count: 6 }, ...]
     */
    public function index(): JsonResponse
    {
        // withCount → tambahkan jumlah produk di setiap kategori
        $categories = Category::withCount('products')->get();

        return response()->json($categories);
    }
}
```

```php
<?php
// app/Http/Controllers/Api/BrandController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    /**
     * GET /api/brands
     */
    public function index(): JsonResponse
    {
        $brands = Brand::withCount('products')->get();

        return response()->json($brands);
    }
}
```

---

### 5.4 CartController — Kelola Keranjang

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * LIHAT CART — Ambil isi keranjang user
     * GET /api/cart
     */
    public function index(Request $request): JsonResponse
    {
        // firstOrCreate = cari cart user, kalau belum ada → buat baru
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        // Load semua item beserta data produknya
        $cart->load(['items.variant.product.images', 'items.variant.product']);

        return response()->json($cart);
    }

    /**
     * TAMBAH ITEM — Masukkan produk ke cart
     * POST /api/cart/items
     * Body: { variant_id: 5, quantity: 2 }
     */
    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity'    => 'required|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        // Cek apakah variant ini sudah ada di cart
        $item = CartItem::where('cart_id', $cart->id)
            ->where('variant_id', $validated['variant_id'])
            ->first();

        if ($item) {
            // Sudah ada → tambah quantity saja
            $item->update([
                'quantity' => $item->quantity + $validated['quantity']
            ]);
        } else {
            // Belum ada → buat item baru
            CartItem::create([
                'cart_id'    => $cart->id,
                'variant_id' => $validated['variant_id'],
                'quantity'   => $validated['quantity'],
            ]);
        }

        $cart->load(['items.variant.product.images', 'items.variant.product']);

        return response()->json($cart);
    }

    /**
     * UPDATE QUANTITY
     * PUT /api/cart/items/3
     * Body: { quantity: 5 }
     */
    public function updateItem(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
        $item = CartItem::where('cart_id', $cart->id)
            ->where('id', $id)->firstOrFail();

        $item->update(['quantity' => $validated['quantity']]);

        $cart->load(['items.variant.product.images', 'items.variant.product']);

        return response()->json($cart);
    }

    /**
     * HAPUS ITEM
     * DELETE /api/cart/items/3
     */
    public function removeItem(Request $request, int $id): JsonResponse
    {
        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
        CartItem::where('cart_id', $cart->id)->where('id', $id)->delete();

        $cart->load(['items.variant.product.images', 'items.variant.product']);

        return response()->json($cart);
    }
}
```

---

### 5.5 WishlistController

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * LIHAT WISHLIST
     * GET /api/wishlists
     */
    public function index(Request $request): JsonResponse
    {
        $wishlists = Wishlist::where('user_id', $request->user()->id)
            ->with(['product.images', 'product.category'])
            ->get();

        return response()->json($wishlists);
    }

    /**
     * TAMBAH KE WISHLIST
     * POST /api/wishlists
     * Body: { product_id: 3 }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // firstOrCreate → kalau sudah ada, tidak duplikat
        $wishlist = Wishlist::firstOrCreate([
            'user_id'    => $request->user()->id,
            'product_id' => $validated['product_id'],
        ]);

        return response()->json($wishlist, 201);
    }

    /**
     * HAPUS DARI WISHLIST
     * DELETE /api/wishlists/5
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        Wishlist::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->delete();

        return response()->json(['message' => 'Dihapus dari wishlist']);
    }
}
```

---

### 5.6 CheckoutController — Proses Pesanan

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Shipping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * CHECKOUT — Buat order dari isi cart
     * POST /api/checkout
     * Body: { recipient_name, phone_number, address, city,
     *         province, postal_code, payment_method }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone_number'   => 'required|string|max:20',
            'address'        => 'required|string',
            'city'           => 'required|string',
            'province'       => 'required|string',
            'postal_code'    => 'required|string|max:10',
            'payment_method' => 'required|in:qris,cod',
        ]);

        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)
            ->with('items.variant.product')
            ->first();

        // Cek cart tidak kosong
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => 'Keranjang kosong'
            ], 422);
        }

        // DB::transaction → kalau ada error, semua dibatalkan
        return DB::transaction(function () use ($validated, $user, $cart) {

            // 1. Hitung total harga
            $totalAmount = 0;
            foreach ($cart->items as $item) {
                $totalAmount += $item->variant->product->price * $item->quantity;
            }

            // 2. Buat order
            $order = Order::create([
                'user_id'        => $user->id,
                'order_number'   => 'ORD-' . strtoupper(Str::random(8)),
                'total_amount'   => $totalAmount,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
            ]);

            // 3. Buat order items + kurangi stok
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'variant_id' => $item->variant_id,
                    'quantity'   => $item->quantity,
                    'item_price' => $item->variant->product->price,
                ]);

                // Kurangi stok variant
                $item->variant->decrement('stock', $item->quantity);
            }

            // 4. Buat payment record
            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => $validated['payment_method'],
                'amount'         => $totalAmount,
                'status'         => 'pending',
            ]);

            // 5. Buat shipping record
            Shipping::create([
                'order_id'       => $order->id,
                'recipient_name' => $validated['recipient_name'],
                'phone_number'   => $validated['phone_number'],
                'address'        => $validated['address'],
                'city'           => $validated['city'],
                'province'       => $validated['province'],
                'postal_code'    => $validated['postal_code'],
                'shipping_cost'  => 0,
            ]);

            // 6. Kosongkan cart
            $cart->items()->delete();

            // 7. Return order lengkap
            $order->load(['items.variant.product', 'payment', 'shipping']);

            return response()->json($order, 201);
        });
    }
}
```

---

### 5.7 OrderController — Lihat Pesanan

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * LIST — Semua order milik user
     * GET /api/orders
     */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items.variant.product.images', 'payment', 'shipping'])
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    /**
     * DETAIL — 1 order
     * GET /api/orders/5
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with(['items.variant.product.images', 'payment', 'shipping'])
            ->findOrFail($id);

        return response()->json($order);
    }

    /**
     * KONFIRMASI BAYAR (untuk QRIS)
     * POST /api/orders/5/confirm-payment
     */
    public function confirmPayment(Request $request, int $id): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with('payment')
            ->findOrFail($id);

        if ($order->payment_status === 'paid') {
            return response()->json(['message' => 'Sudah dibayar'], 422);
        }

        $order->update([
            'payment_status' => 'paid',
            'status'         => 'paid',
        ]);

        $order->payment->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);

        return response()->json($order->load(['items.variant.product.images', 'payment']));
    }
}
```

---

## 6. Routes — Mendaftarkan Endpoint

File: **`routes/api.php`**

```php
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|-----------------------------------------------
| PUBLIC ROUTES — Bisa diakses tanpa login
|-----------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{slug}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/brands', [BrandController::class, 'index']);

/*
|-----------------------------------------------
| PROTECTED ROUTES — Harus login (kirim token)
|-----------------------------------------------
| middleware('auth:sanctum') → cek token valid
*/
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartController::class, 'addItem']);
    Route::put('/cart/items/{id}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{id}', [CartController::class, 'removeItem']);

    // Wishlist
    Route::get('/wishlists', [WishlistController::class, 'index']);
    Route::post('/wishlists', [WishlistController::class, 'store']);
    Route::delete('/wishlists/{id}', [WishlistController::class, 'destroy']);

    // Checkout & Orders
    Route::post('/checkout', [CheckoutController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/confirm-payment', [OrderController::class, 'confirmPayment']);
});
```

---

## 7. Testing API

### Cara test pakai Terminal (curl)

```bash
# 1. Ambil produk (public, tanpa login)
curl http://localhost:8000/api/products

# 2. Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John","email":"john@test.com","password":"12345678","password_confirmation":"12345678"}'

# Response: { "user": {...}, "token": "1|abc123..." }

# 3. Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@test.com","password":"12345678"}'

# 4. Akses route protected (pakai token dari step 2/3)
curl http://localhost:8000/api/cart \
  -H "Authorization: Bearer 1|abc123..."

# 5. Tambah ke cart
curl -X POST http://localhost:8000/api/cart/items \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|abc123..." \
  -d '{"variant_id": 1, "quantity": 2}'
```

### Cara test pakai Postman
1. Buka Postman → New Request
2. Pilih method (GET/POST/PUT/DELETE)
3. Masukkan URL: `http://localhost:8000/api/products`
4. Untuk route protected: Tab **Authorization** → Type: **Bearer Token** → paste token
5. Untuk kirim data: Tab **Body** → **raw** → **JSON**

---

## 8. Rangkuman Pola

Setiap controller API mengikuti pola yang sama:

```php
// 1. Validasi input (kalau ada)
$validated = $request->validate([...]);

// 2. Proses (query database)
$data = Model::where(...)->get();

// 3. Return JSON
return response()->json($data);
```

Untuk route yang butuh user login:
```php
// Ambil user yang sedang login
$user = $request->user();

// Pastikan data milik user ini
$orders = Order::where('user_id', $user->id)->get();
```
