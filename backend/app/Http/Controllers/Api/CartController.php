<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CartController — Menangani endpoint API untuk keranjang belanja.
 * Semua endpoint membutuhkan autentikasi (auth:sanctum).
 */
class CartController extends Controller
{
    /**
     * Lihat isi keranjang. Cart dibuat otomatis jika belum ada.
     * GET /api/cart
     */
    public function index(Request $request): JsonResponse
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $cart->load(['items.variant.product.images', 'items.variant.product']);
        return response()->json($cart);
    }

    /**
     * Tambah item ke keranjang. Jika sudah ada, quantity di-increment.
     * POST /api/cart/items — Body: variant_id, quantity
     */
    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $item = CartItem::where('cart_id', $cart->id)
            ->where('variant_id', $validated['variant_id'])->first();
        if ($item) {
            $item->update(['quantity' => $item->quantity + $validated['quantity']]);
        } else {
            $item = CartItem::create([
                'cart_id' => $cart->id,
                'variant_id' => $validated['variant_id'],
                'quantity' => $validated['quantity'],
                'created_at' => now(),
            ]);
        }
        $cart->load(['items.variant.product.images', 'items.variant.product']);
        return response()->json($cart);
    }

    /**
     * Update jumlah item di keranjang.
     * PUT /api/cart/items/{id} — Body: quantity
     */
    public function updateItem(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate(['quantity' => 'required|integer|min:1']);
        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
        $item = CartItem::where('cart_id', $cart->id)->where('id', $id)->firstOrFail();
        $item->update(['quantity' => $validated['quantity']]);
        $cart->load(['items.variant.product.images', 'items.variant.product']);
        return response()->json($cart);
    }

    /**
     * Hapus item dari keranjang.
     * DELETE /api/cart/items/{id}
     */
    public function removeItem(Request $request, int $id): JsonResponse
    {
        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
        CartItem::where('cart_id', $cart->id)->where('id', $id)->delete();
        $cart->load(['items.variant.product.images', 'items.variant.product']);
        return response()->json($cart);
    }
}
