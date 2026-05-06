# 📘 Walkthrough Backend Solevia

Dokumen ini menjelaskan **alur kerja pembuatan backend** Solevia secara sistematis, dari setup environment hingga API & admin panel siap digunakan.

---

## Daftar Isi

1. [Environment](#1-environment)
2. [Migration](#2-migration)
3. [Models](#3-models)
4. [Controllers](#4-controllers)
5. [Routes](#5-routes)
6. [Filament Resources](#6-filament-resources)
7. [Alur HTTP Request](#7-alur-http-request)

---

## 1. Environment

**File:** `.env`

Environment adalah file konfigurasi yang menyimpan **variabel sensitif** (password, key) dan **pengaturan lokal** yang berbeda di setiap mesin. Laravel membaca file ini untuk menentukan bagaimana aplikasi berjalan.

### Variabel Penting di Solevia

```env
# Identitas Aplikasi
APP_NAME=Laravel          # Nama app (sebaiknya diganti ke "Solevia")
APP_ENV=local             # Mode: local / production
APP_DEBUG=true            # Tampilkan error detail (matikan di production)
APP_URL=http://localhost:8000    # URL backend

# Koneksi Frontend
FRONTEND_URL=http://localhost:5173
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost:8000

# Database MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=solevia       # Nama database
DB_USERNAME=athayadb
DB_PASSWORD=12345

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# File Storage
FILESYSTEM_DISK=local     # Default disk untuk file upload
```

### Fungsi Utama

| Variabel | Fungsi |
|----------|--------|
| `APP_KEY` | Kunci enkripsi untuk session, password, dll. Di-generate via `php artisan key:generate` |
| `DB_*` | Koneksi ke database MySQL bernama `solevia` |
| `SANCTUM_STATEFUL_DOMAINS` | Domain yang boleh menggunakan cookie auth (frontend Vue di port 5173) |
| `SESSION_DRIVER=database` | Session disimpan di tabel database, bukan file |

> **Alur:** Laravel boot → baca `.env` → konfigurasi di `config/*.php` mengambil nilai dari `env()` → aplikasi berjalan sesuai setting.

---

## 2. Migration

**Folder:** `database/migrations/`

Migration adalah **"version control" untuk database**. Setiap file migration mendefinisikan satu tabel, termasuk kolom, tipe data, dan foreign key. Dijalankan dengan `php artisan migrate`.

### Urutan Migration Solevia

```
1.  create_users_table          → Tabel user (auth)
2.  create_cache_table          → Cache storage
3.  create_jobs_table           → Queue jobs
4.  create_categories_table     → Kategori produk
5.  create_brands_table         → Brand/merek
6.  create_products_table       → Produk utama
7.  create_product_variants_table → Varian (ukuran + stok)
8.  create_product_images_table → Gambar produk
9.  create_carts_table          → Keranjang belanja
10. create_cart_items_table     → Item di keranjang
11. create_orders_table         → Pesanan
12. create_order_items_table    → Item di pesanan
13. create_payments_table       → Pembayaran
14. create_wishlists_table      → Wishlist
15. create_shippings_table      → Info pengiriman
16. create_personal_access_tokens_table → Token Sanctum
17. add_discount_price_to_products_table → Tambah kolom diskon
```

### Contoh: Migration Products

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();                              // Primary key auto-increment
    $table->string('name');                    // Nama produk
    $table->string('slug')->unique();          // URL-friendly name
    $table->text('description')->nullable();   // Deskripsi
    $table->decimal('price', 12, 2);           // Harga (max 12 digit, 2 desimal)
    $table->foreignId('category_id')           // Foreign key ke categories
          ->constrained()->cascadeOnDelete();
    $table->foreignId('brand_id')              // Foreign key ke brands
          ->constrained()->cascadeOnDelete();
    $table->timestamps();                      // created_at & updated_at
});
```

### Contoh: Migration Orders

```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('order_number')->unique();  // Nomor pesanan unik
    $table->decimal('total_amount', 14, 2);    // Total harga
    $table->string('status')->default('pending');         // pending/paid/shipped/completed/cancelled
    $table->string('payment_status')->default('unpaid');  // unpaid/paid
    $table->timestamps();
});
```

### Relasi Antar Tabel

```
Users ──1:1──► Cart ──1:N──► CartItems ──N:1──► ProductVariants
Users ──1:N──► Orders ──1:N──► OrderItems ──N:1──► ProductVariants
                 │──1:1──► Payments
                 └──1:1──► Shippings
Products ──1:N──► ProductVariants
Products ──1:N──► ProductImages
Products ──N:1──► Categories
Products ──N:1──► Brands
Users ──1:N──► Wishlists ──N:1──► Products
```

> **Alur:** Tulis migration → `php artisan migrate` → Laravel membuat tabel di MySQL sesuai definisi.

---

## 3. Models

**Folder:** `app/Models/`

Model adalah **representasi PHP dari tabel database**. Setiap model merepresentasikan satu tabel dan mendefinisikan:
- **$fillable** — kolom yang boleh diisi massal
- **$casts** — konversi tipe data otomatis
- **Relationships** — relasi antar tabel menggunakan method

### Daftar Model (13 total)

| Model | Tabel | Fungsi |
|-------|-------|--------|
| `User` | users | Akun pengguna, implementasi Sanctum & Filament |
| `Product` | products | Data produk dengan auto-generate slug |
| `ProductVariant` | product_variants | Ukuran & stok per produk |
| `ProductImage` | product_images | Gambar produk (multiple) |
| `Category` | categories | Kategori (Sneakers, Running, dll) |
| `Brand` | brands | Merek (Nike, Adidas, dll) |
| `Cart` | carts | Keranjang belanja per user |
| `CartItem` | cart_items | Item di dalam keranjang |
| `Order` | orders | Pesanan yang dibuat saat checkout |
| `OrderItem` | order_items | Detail item per pesanan |
| `Payment` | payments | Info pembayaran per order |
| `Shipping` | shippings | Info pengiriman per order |
| `Wishlist` | wishlists | Produk favorit user |

### Contoh: Model Product

```php
class Product extends Model
{
    // Kolom yang boleh diisi via create() / update()
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'discount_price',
        'category_id', 'brand_id',
    ];

    // Auto-cast tipe data
    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    // Auto-generate slug saat membuat produk baru
    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // === RELASI ===
    public function category(): BelongsTo    // Produk milik 1 kategori
    { return $this->belongsTo(Category::class); }

    public function brand(): BelongsTo       // Produk milik 1 brand
    { return $this->belongsTo(Brand::class); }

    public function variants(): HasMany      // Produk punya banyak varian
    { return $this->hasMany(ProductVariant::class); }

    public function images(): HasMany        // Produk punya banyak gambar
    { return $this->hasMany(ProductImage::class); }
}
```

### Contoh: Model User

```php
class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;
    // HasApiTokens → bisa membuat token Sanctum
    // FilamentUser → bisa akses admin panel

    public function canAccessPanel(Panel $panel): bool
    { return true; } // Semua user bisa akses admin (⚠️ harus dibatasi)

    public function cart(): HasOne      // User punya 1 cart
    { return $this->hasOne(Cart::class); }

    public function orders(): HasMany   // User punya banyak order
    { return $this->hasMany(Order::class); }

    public function wishlists(): HasMany // User punya banyak wishlist
    { return $this->hasMany(Wishlist::class); }
}
```

> **Alur:** Controller butuh data → panggil Model → Model query ke database via Eloquent ORM → return hasil sebagai object PHP.

---

## 4. Controllers

**Folder:** `app/Http/Controllers/Api/`

Controller adalah **otak logika aplikasi**. Menerima request dari route, memproses data (validasi, query, business logic), lalu mengembalikan response JSON.

### Daftar Controller (8 total)

#### 4.1 AuthController — Autentikasi

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| `register()` | POST /register | Buat akun baru, return user + token |
| `login()` | POST /login | Verifikasi email/password, return token |
| `logout()` | POST /logout | Hapus token yang sedang dipakai |
| `user()` | GET /user | Return data user yang login |

```php
// Login: validasi → cek credentials → buat token → return
public function login(Request $request): JsonResponse
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $user = User::where('email', $request->email)->firstOrFail();
    $token = $user->createToken('auth-token')->plainTextToken;

    return response()->json(['user' => $user, 'token' => $token]);
}
```

#### 4.2 ProductController — Katalog Produk

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| `index()` | GET /products | List produk + filter (category, brand, search) + pagination |
| `show()` | GET /products/{slug} | Detail produk by slug |

```php
// List: eager load relasi → filter opsional → paginate
public function index(Request $request): JsonResponse
{
    $query = Product::with(['category', 'brand', 'images', 'variants']);

    if ($request->has('category_id'))
        $query->where('category_id', $request->category_id);
    if ($request->has('brand_id'))
        $query->where('brand_id', $request->brand_id);
    if ($request->has('search'))
        $query->where('name', 'like', '%' . $request->search . '%');

    return response()->json($query->latest()->paginate(12));
}
```

#### 4.3 CartController — Keranjang Belanja

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| `index()` | GET /cart | Lihat isi cart |
| `addItem()` | POST /cart/items | Tambah item (jika sudah ada, tambah qty) |
| `updateItem()` | PUT /cart/items/{id} | Ubah quantity |
| `removeItem()` | DELETE /cart/items/{id} | Hapus item dari cart |

#### 4.4 CheckoutController — Proses Checkout

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| `store()` | POST /checkout | Buat order dari isi cart |

```php
// Checkout: validasi → hitung total → buat order + items + payment + shipping → kurangi stok → kosongkan cart
// Semua dibungkus DB::transaction() agar atomic (gagal = rollback semua)
return DB::transaction(function () use ($validated, $user, $cart) {
    $totalAmount = 0;
    foreach ($cart->items as $item) {
        $totalAmount += $item->variant->product->price * $item->quantity;
    }

    $order = Order::create([...]);       // 1. Buat order
    foreach ($cart->items as $item) {
        OrderItem::create([...]);        // 2. Buat order items
        $item->variant->decrement('stock', $item->quantity); // 3. Kurangi stok
    }
    Payment::create([...]);              // 4. Buat payment record
    Shipping::create([...]);             // 5. Buat shipping record
    $cart->items()->delete();            // 6. Kosongkan cart

    return response()->json($order, 201);
});
```

#### 4.5 OrderController — Riwayat Pesanan

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| `index()` | GET /orders | List semua order milik user |
| `show()` | GET /orders/{id} | Detail order |
| `confirmPayment()` | POST /orders/{id}/confirm-payment | Ubah status jadi "paid" |

#### 4.6 Lainnya

- **WishlistController** — CRUD wishlist (index, store, destroy)
- **CategoryController** — List kategori
- **BrandController** — List & detail brand

> **Alur:** Route cocok → Controller method dipanggil → validasi input → query via Model → return `response()->json()`

---

## 5. Routes

**File:** `routes/api.php`

Route adalah **peta URL** yang menghubungkan setiap endpoint HTTP ke method controller yang tepat. Laravel secara otomatis menambahkan prefix `/api` untuk file ini.

### Struktur Routes

```php
// ═══════════════════════════════════════
// PUBLIC ROUTES (tanpa login)
// ═══════════════════════════════════════
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::get('/products',       [ProductController::class, 'index']);
Route::get('/products/{slug}',[ProductController::class, 'show']);
Route::get('/categories',     [CategoryController::class, 'index']);
Route::get('/brands',         [BrandController::class, 'index']);
Route::get('/brands/{id}',    [BrandController::class, 'show']);

// ═══════════════════════════════════════
// PROTECTED ROUTES (harus login + kirim token)
// ═══════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/user',    [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cart
    Route::get('/cart',              [CartController::class, 'index']);
    Route::post('/cart/items',       [CartController::class, 'addItem']);
    Route::put('/cart/items/{id}',   [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{id}',[CartController::class, 'removeItem']);

    // Wishlist
    Route::get('/wishlists',         [WishlistController::class, 'index']);
    Route::post('/wishlists',        [WishlistController::class, 'store']);
    Route::delete('/wishlists/{id}', [WishlistController::class, 'destroy']);

    // Checkout & Orders
    Route::post('/checkout',                      [CheckoutController::class, 'store']);
    Route::get('/orders',                         [OrderController::class, 'index']);
    Route::get('/orders/{id}',                    [OrderController::class, 'show']);
    Route::post('/orders/{id}/confirm-payment',   [OrderController::class, 'confirmPayment']);
});
```

### Penjelasan Middleware

```
auth:sanctum → Cek apakah request memiliki token valid
             → Jika tidak ada token / token expired → return 401 Unauthorized
             → Jika valid → lanjut ke controller, $request->user() tersedia
```

### HTTP Methods

| Method | Fungsi | Contoh |
|--------|--------|--------|
| `GET` | Ambil data (read) | GET /products |
| `POST` | Buat data baru (create) | POST /cart/items |
| `PUT` | Update data (update) | PUT /cart/items/1 |
| `DELETE` | Hapus data (delete) | DELETE /wishlists/5 |

> **Alur:** Frontend kirim HTTP request → Laravel cocokkan URL & method di routes → cek middleware → panggil controller.

---

## 6. Filament Resources

**Folder:** `app/Filament/Resources/`

Filament Resource adalah **definisi halaman admin CRUD** untuk setiap entitas. Setiap resource terdiri dari:
- **form()** — Form untuk create/edit
- **table()** — Tampilan daftar data
- **infolist()** — Tampilan detail (opsional)
- **Pages/** — Halaman List, Create, Edit, View

### Daftar Resources

| Resource | Nav Group | Fungsi |
|----------|-----------|--------|
| ProductResource | Catalog | CRUD produk + upload gambar + kelola varian |
| BrandResource | Catalog | CRUD brand |
| CategoryResource | Catalog | CRUD kategori |
| OrderResource | Orders | View & edit status order |
| UserResource | — | Manajemen user |

### Contoh: ProductResource

```php
// FORM: Dipakai di halaman Create & Edit
public static function form(Schema $schema): Schema
{
    return $schema->components([
        // Section 1: Info Produk
        Section::make('Product Information')->schema([
            TextInput::make('name')->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state) =>
                    $set('slug', Str::slug($state))),  // Auto-generate slug
            TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Textarea::make('description'),
            TextInput::make('price')->required()->numeric()->prefix('Rp'),
            TextInput::make('discount_price')->numeric()->prefix('Rp'),
            Select::make('category_id')->relationship('category', 'name')
                ->required()->searchable()->preload(),
            Select::make('brand_id')->relationship('brand', 'name')
                ->required()->searchable()->preload(),
        ])->columns(2),

        // Section 2: Upload Gambar
        Section::make('Product Images')->schema([
            Repeater::make('images')->relationship()->schema([
                FileUpload::make('image_url')->image()
                    ->directory('products')->disk('public'),
            ])->minItems(1),
        ]),

        // Section 3: Varian (Size & Stock)
        Section::make('Variants')->schema([
            Repeater::make('variants')->relationship()->schema([
                TextInput::make('size')->required(),
                TextInput::make('stock')->required()->numeric()->default(0),
            ])->columns(2)->minItems(1),
        ]),
    ]);
}

// TABLE: Dipakai di halaman List
public static function table(Table $table): Table
{
    return $table->columns([
        ImageColumn::make('images.image_url')->circular()->limit(1),
        TextColumn::make('name')->searchable()->sortable(),
        TextColumn::make('price')->money('IDR')->sortable(),
        TextColumn::make('category.name')->sortable(),
        TextColumn::make('brand.name')->sortable(),
        TextColumn::make('variants_count')->counts('variants'),
    ])
    ->filters([
        SelectFilter::make('category_id')->relationship('category', 'name'),
        SelectFilter::make('brand_id')->relationship('brand', 'name'),
    ]);
}
```

### Dashboard Widgets

| Widget | Fungsi |
|--------|--------|
| `StatsOverview` | Kartu statistik: total revenue, orders, products, customers |
| `SalesChart` | Grafik penjualan |
| `LatestOrders` | Tabel order terbaru |

> **Alur:** User buka `/admin` → Filament render dashboard + sidebar → klik resource → Filament auto-generate halaman CRUD berdasarkan definisi form() dan table().

---

## 7. Alur HTTP Request

Berikut alur lengkap dari frontend mengirim request sampai mendapat response:

### Contoh: User Menambah Item ke Cart

```
┌─────────────┐
│   FRONTEND  │  POST /api/cart/items
│   (Vue.js)  │  Headers: { Authorization: Bearer <token> }
│             │  Body: { variant_id: 3, quantity: 2 }
└──────┬──────┘
       │
       ▼
┌─────────────┐
│    CORS     │  config/cors.php mengecek apakah origin diizinkan
│  Middleware  │  ✅ allowed_origins: ['*']
└──────┬──────┘
       │
       ▼
┌─────────────┐
│   ROUTE     │  routes/api.php mencocokkan:
│  Matching   │  POST /cart/items → CartController@addItem
└──────┬──────┘
       │
       ▼
┌─────────────┐
│ auth:sanctum│  Middleware mengecek token di header
│ Middleware  │  ✅ Token valid → set $request->user()
└──────┬──────┘
       │
       ▼
┌──────────────┐
│  CONTROLLER  │  CartController::addItem()
│              │  1. Validasi input (variant_id exists, quantity min 1)
│              │  2. Cari/buat cart untuk user
│              │  3. Cek apakah item sudah ada di cart
│              │     → Ya: tambah quantity
│              │     → Tidak: buat CartItem baru
│              │  4. Load relasi untuk response
└──────┬───────┘
       │
       ▼
┌──────────────┐
│    MODEL     │  Cart::firstOrCreate() → query ke tabel `carts`
│  (Eloquent)  │  CartItem::create()    → insert ke tabel `cart_items`
│              │  $cart->load(...)       → eager load relasi
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   DATABASE   │  MySQL menjalankan SQL query:
│   (MySQL)    │  SELECT/INSERT/UPDATE pada tabel terkait
└──────┬───────┘
       │
       ▼
┌──────────────┐
│   RESPONSE   │  return response()->json($cart)
│   (JSON)     │  Status: 200 OK
│              │  Body: { id, user_id, items: [...] }
└──────────────┘
```

### Contoh: Alur Checkout

```
Frontend POST /api/checkout
    │
    ▼
auth:sanctum ✅ → CheckoutController::store()
    │
    ├─ 1. Validasi: nama, telepon, alamat, kota, provinsi, kode pos, metode bayar
    ├─ 2. Ambil cart + items milik user
    ├─ 3. Cek cart tidak kosong
    │
    ├─ DB::transaction() mulai ─────────────────────────────┐
    │   ├─ 4. Hitung total harga                            │
    │   ├─ 5. Buat Order (status: pending, unpaid)          │
    │   ├─ 6. Buat OrderItems untuk setiap cart item        │
    │   ├─ 7. Kurangi stok variant (decrement)              │
    │   ├─ 8. Buat Payment record (status: pending)         │
    │   ├─ 9. Buat Shipping record (info alamat)            │
    │   └─ 10. Hapus semua cart items                       │
    │   └────── Commit (atau rollback jika error) ──────────┘
    │
    └─ 11. Return order + relasi (201 Created)
```

---

## 📋 Ringkasan Alur Keseluruhan

```
Environment (.env)
    └─► Konfigurasi koneksi DB, auth, CORS
         └─► Migration
              └─► Membuat tabel di MySQL
                   └─► Models
                        └─► Representasi tabel sebagai class PHP + relasi
                             └─► Controllers
                                  └─► Logika bisnis: validasi, query, response
                                       └─► Routes (api.php)
                                            └─► Peta URL → Controller method
                                                 └─► Filament Resources
                                                      └─► Admin panel CRUD auto-generated
```

| Langkah | Apa yang dilakukan | Perintah |
|---------|-------------------|----------|
| 1 | Setup environment | Copy `.env.example` → `.env`, isi DB credentials |
| 2 | Generate app key | `php artisan key:generate` |
| 3 | Buat migration files | `php artisan make:migration create_xxx_table` |
| 4 | Jalankan migration | `php artisan migrate` |
| 5 | Buat models | `php artisan make:model Product` |
| 6 | Definisikan relasi | Tambah method relationship di model |
| 7 | Buat controllers | `php artisan make:controller Api/ProductController` |
| 8 | Tulis logika di controller | Validasi, query, response JSON |
| 9 | Daftarkan routes | Tulis endpoint di `routes/api.php` |
| 10 | Buat Filament resources | `php artisan make:filament-resource Product` |
| 11 | Konfigurasi form & table | Definisikan kolom, filter, relasi di resource |
| 12 | Seed data awal | `php artisan db:seed` |
| 13 | Jalankan server | `php artisan serve` |
