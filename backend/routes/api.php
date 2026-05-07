<?php

/**
 * ============================================================
 * File: routes/api.php
 * ============================================================
 *
 * File ini mendefinisikan semua endpoint API untuk aplikasi Solevia.
 * Semua route di file ini secara otomatis mendapat prefix '/api'.
 * Contoh: Route::get('/products') → dapat diakses di: GET /api/products
 *
 * Route dibagi menjadi 2 kelompok:
 * 1. PUBLIC ROUTES    → Bisa diakses tanpa login (tanpa token).
 * 2. PROTECTED ROUTES → Harus login dulu dan mengirim Bearer Token di header.
 *
 * Middleware 'auth:sanctum' digunakan untuk melindungi route yang membutuhkan autentikasi.
 * Token didapat dari response login/register dan dikirim via header:
 *   Authorization: Bearer <token>
 */

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

// ============================================================
// PUBLIC ROUTES — Tidak perlu autentikasi
// ============================================================

// Autentikasi: register akun baru dan login
Route::post('/register', [AuthController::class, 'register']);  // POST /api/register → Daftar akun baru
Route::post('/login', [AuthController::class, 'login']);        // POST /api/login    → Login dan dapat token

// Produk: bisa diakses siapa saja (termasuk pengunjung tanpa akun)
Route::get('/products', [ProductController::class, 'index']);       // GET /api/products       → Daftar semua produk (+ filter & search)
Route::get('/products/{slug}', [ProductController::class, 'show']); // GET /api/products/{slug} → Detail produk berdasarkan slug

// Kategori dan Brand: untuk filter dan navigasi
Route::get('/categories', [CategoryController::class, 'index']);   // GET /api/categories    → Daftar semua kategori + jumlah produk
Route::get('/brands', [BrandController::class, 'index']);          // GET /api/brands        → Daftar semua brand + jumlah produk
Route::get('/brands/{id}', [BrandController::class, 'show']);      // GET /api/brands/{id}   → Detail 1 brand

// ============================================================
// PROTECTED ROUTES — Harus login (auth:sanctum)
// ============================================================
Route::middleware('auth:sanctum')->group(function () {
    // Profil user yang sedang login
    Route::get('/user', [AuthController::class, 'user']);       // GET  /api/user   → Data user yang sedang login
    Route::post('/logout', [AuthController::class, 'logout']);  // POST /api/logout → Logout (hapus token)

    // Cart (Keranjang Belanja)
    Route::get('/cart', [CartController::class, 'index']);              // GET    /api/cart          → Lihat isi keranjang
    Route::post('/cart/items', [CartController::class, 'addItem']);     // POST   /api/cart/items    → Tambah item ke keranjang
    Route::put('/cart/items/{id}', [CartController::class, 'updateItem']);    // PUT  /api/cart/items/{id} → Update jumlah item
    Route::delete('/cart/items/{id}', [CartController::class, 'removeItem']); // DELETE /api/cart/items/{id} → Hapus item dari keranjang

    // Wishlist (Produk Favorit)
    Route::get('/wishlists', [WishlistController::class, 'index']);      // GET    /api/wishlists     → Daftar wishlist user
    Route::post('/wishlists', [WishlistController::class, 'store']);     // POST   /api/wishlists     → Tambah produk ke wishlist
    Route::delete('/wishlists/{id}', [WishlistController::class, 'destroy']); // DELETE /api/wishlists/{id} → Hapus dari wishlist

    // Checkout & Orders (Pesanan)
    Route::post('/checkout', [CheckoutController::class, 'store']);                  // POST /api/checkout               → Proses checkout (buat pesanan)
    Route::get('/orders', [OrderController::class, 'index']);                        // GET  /api/orders                 → Daftar pesanan user
    Route::get('/orders/{id}', [OrderController::class, 'show']);                    // GET  /api/orders/{id}            → Detail 1 pesanan
    Route::post('/orders/{id}/confirm-payment', [OrderController::class, 'confirmPayment']); // POST /api/orders/{id}/confirm-payment → Konfirmasi pembayaran
});
