<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;

/**
 * BrandController — Menangani endpoint API untuk brand/merek produk.
 *
 * Menyediakan 2 endpoint publik:
 * - index → Daftar semua brand dengan jumlah produk.
 * - show  → Detail 1 brand berdasarkan ID.
 */
class BrandController extends Controller
{
    /**
     * Index — Menampilkan semua brand beserta jumlah produk masing-masing.
     *
     * Endpoint: GET /api/brands (Public — tanpa login)
     *
     * Response contoh:
     * [
     *   { "id": 1, "name": "Nike", "description": "Just Do It", "products_count": 3 },
     *   { "id": 2, "name": "Adidas", "description": "...", "products_count": 2 }
     * ]
     */
    public function index(): JsonResponse
    {
        // withCount('products') menghitung jumlah produk per brand secara efisien
        $brands = Brand::withCount('products')->get();

        return response()->json($brands);
    }

    /**
     * Show — Menampilkan detail 1 brand berdasarkan ID.
     *
     * Endpoint: GET /api/brands/{id} (Public — tanpa login)
     *
     * Jika brand tidak ditemukan → return 404 Not Found otomatis (findOrFail).
     */
    public function show(int $id): JsonResponse
    {
        $brand = Brand::findOrFail($id);
        return response()->json($brand);
    }
}
