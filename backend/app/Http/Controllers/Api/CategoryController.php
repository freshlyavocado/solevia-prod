<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

/**
 * CategoryController — Menangani endpoint API untuk kategori produk.
 *
 * Hanya memiliki 1 endpoint publik untuk menampilkan semua kategori.
 * Digunakan oleh frontend untuk menampilkan filter kategori dan navigasi.
 */
class CategoryController extends Controller
{
    /**
     * Index — Menampilkan semua kategori beserta jumlah produk masing-masing.
     *
     * Endpoint: GET /api/categories (Public — tanpa login)
     *
     * Response contoh:
     * [
     *   { "id": 1, "name": "Sneakers", "description": "...", "products_count": 5 },
     *   { "id": 2, "name": "Running", "description": "...", "products_count": 2 }
     * ]
     *
     * withCount('products') menambahkan kolom 'products_count' yang berisi
     * jumlah produk di setiap kategori (tanpa perlu query tambahan).
     */
    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')->get();

        return response()->json($categories);
    }
}
