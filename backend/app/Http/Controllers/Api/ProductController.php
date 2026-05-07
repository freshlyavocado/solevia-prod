<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ProductController — Menangani endpoint API untuk produk.
 *
 * Controller ini menyediakan 2 endpoint publik (tanpa login):
 * - index → Daftar semua produk (dengan filter, search, dan pagination).
 * - show  → Detail 1 produk berdasarkan slug URL.
 *
 * Setiap response produk di-load beserta relasi: category, brand, images, variants.
 */
class ProductController extends Controller
{
    /**
     * Index — Menampilkan daftar semua produk dengan filter dan pagination.
     *
     * Endpoint: GET /api/products
     *
     * Query parameters (opsional):
     * - category_id → Filter berdasarkan kategori (misal: ?category_id=1)
     * - brand_id    → Filter berdasarkan brand (misal: ?brand_id=2)
     * - search      → Cari produk berdasarkan nama (misal: ?search=air+max)
     * - per_page    → Jumlah produk per halaman (default: 12)
     *
     * Response: Data produk dengan pagination (data, current_page, last_page, total, dll).
     * Setiap produk sudah termasuk relasi: category, brand, images, variants.
     */
    public function index(Request $request): JsonResponse
    {
        // Mulai query dengan eager loading relasi untuk menghindari N+1 query problem
        $query = Product::with(['category', 'brand', 'images', 'variants']);

        // Filter berdasarkan kategori jika parameter 'category_id' ada
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter berdasarkan brand jika parameter 'brand_id' ada
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter pencarian berdasarkan nama produk (LIKE query)
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Urutkan dari terbaru (latest) dan paginate hasilnya
        // Default 12 produk per halaman, bisa diubah via query ?per_page=24
        $products = $query->latest()->paginate($request->get('per_page', 12));

        return response()->json($products);
    }

    /**
     * Show — Menampilkan detail 1 produk berdasarkan slug.
     *
     * Endpoint: GET /api/products/{slug}
     *
     * Parameter:
     * - slug → Slug URL produk (misal: "air-max-90")
     *
     * Response: Data lengkap 1 produk dengan category, brand, images, dan variants.
     * Jika produk tidak ditemukan → return 404 Not Found.
     */
    public function show(string $slug): JsonResponse
    {
        // Cari produk berdasarkan slug dengan eager loading relasi
        // firstOrFail() akan otomatis return 404 jika tidak ditemukan
        $product = Product::with(['category', 'brand', 'images', 'variants'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($product);
    }
}
