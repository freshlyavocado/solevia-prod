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

/**
 * CheckoutController — Memproses checkout dan membuat pesanan (Order).
 *
 * Alur checkout:
 * 1. Validasi input (alamat pengiriman + metode bayar).
 * 2. Ambil isi keranjang user.
 * 3. Hitung total harga dari semua item di keranjang.
 * 4. Buat Order baru dengan nomor unik (ORD-XXXXXXXX).
 * 5. Buat OrderItem untuk setiap item di keranjang.
 * 6. Kurangi stok varian produk.
 * 7. Buat data Payment (status: pending).
 * 8. Buat data Shipping (alamat pengiriman).
 * 9. Kosongkan keranjang user.
 *
 * Semua langkah dibungkus dalam DB::transaction() agar atomik
 * (jika satu langkah gagal, semua dibatalkan/rollback).
 */
class CheckoutController extends Controller
{
    /**
     * Store — Proses checkout dan buat pesanan baru.
     * POST /api/checkout (Protected)
     *
     * Body: recipient_name, phone_number, address, city, province,
     *       postal_code, payment_method (qris/cod)
     */
    public function store(Request $request): JsonResponse
    {
        // Validasi semua data pengiriman dan metode pembayaran
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'payment_method' => 'required|in:qris,cod',
        ]);

        $user = $request->user();

        // Ambil keranjang user beserta item dan data produk
        $cart = Cart::where('user_id', $user->id)->with('items.variant.product')->first();

        // Cek apakah keranjang kosong
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        // Gunakan database transaction (semua berhasil atau semua dibatalkan)
        return DB::transaction(function () use ($validated, $user, $cart) {
            // Hitung total harga dari semua item di keranjang
            $totalAmount = 0;
            foreach ($cart->items as $item) {
                $totalAmount += $item->variant->product->price * $item->quantity;
            }

            // Buat pesanan baru dengan nomor unik
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            // Buat item pesanan dan kurangi stok
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'item_price' => $item->variant->product->price,
                ]);

                // Kurangi stok varian produk
                $item->variant->decrement('stock', $item->quantity);
            }

            // Buat data pembayaran (status awal: pending)
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'amount' => $totalAmount,
                'status' => 'pending',
            ]);

            // Buat data pengiriman
            Shipping::create([
                'order_id' => $order->id,
                'recipient_name' => $validated['recipient_name'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'postal_code' => $validated['postal_code'],
                'shipping_cost' => 0, // Ongkir masih hardcoded 0
                'created_at' => now(),
            ]);

            // Kosongkan keranjang setelah checkout berhasil
            $cart->items()->delete();

            // Load relasi untuk response
            $order->load(['items.variant.product', 'payment', 'shipping']);

            return response()->json($order, 201);
        });
    }
}
