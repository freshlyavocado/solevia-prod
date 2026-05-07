<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * WishlistController — Menangani endpoint API untuk wishlist (produk favorit).
 *
 * Endpoint (Protected — butuh auth):
 * - index   → Daftar wishlist user.
 * - store   → Tambah produk ke wishlist (tidak duplikat).
 * - destroy → Hapus produk dari wishlist.
 */
class WishlistController extends Controller
{
    /**
     * Daftar semua wishlist user, termasuk data produk lengkap.
     * GET /api/wishlists
     */
    public function index(Request $request): JsonResponse
    {
        $wishlists = Wishlist::where('user_id', $request->user()->id)
            ->with(['product.images', 'product.category', 'product.brand'])
            ->latest('created_at')
            ->get();

        return response()->json($wishlists);
    }

    /**
     * Tambah produk ke wishlist. firstOrCreate mencegah duplikasi.
     * POST /api/wishlists — Body: product_id
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // firstOrCreate: jika sudah ada (user_id + product_id), tidak dibuat lagi
        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
        ], [
            'created_at' => now(),
        ]);

        return response()->json($wishlist, 201);
    }

    /**
     * Hapus item dari wishlist berdasarkan ID.
     * DELETE /api/wishlists/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        // Hanya hapus wishlist milik user yang login
        Wishlist::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->delete();

        return response()->json(['message' => 'Removed from wishlist']);
    }
}
