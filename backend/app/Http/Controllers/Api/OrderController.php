<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * OrderController — Menangani endpoint API untuk pesanan user.
 *
 * Endpoint (Protected — butuh auth):
 * - index          → Daftar semua pesanan user (paginated).
 * - show           → Detail 1 pesanan.
 * - confirmPayment → Konfirmasi pembayaran pesanan.
 */
class OrderController extends Controller
{
    /**
     * Daftar semua pesanan milik user yang login, diurutkan dari terbaru.
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
     * Detail 1 pesanan berdasarkan ID. Hanya bisa akses pesanan milik sendiri.
     * GET /api/orders/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with(['items.variant.product.images', 'payment', 'shipping'])
            ->findOrFail($id);

        return response()->json($order);
    }

    /**
     * Konfirmasi pembayaran pesanan. Mengubah status menjadi 'paid'.
     * POST /api/orders/{id}/confirm-payment
     */
    public function confirmPayment(Request $request, int $id): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with('payment')
            ->findOrFail($id);

        // Cek apakah sudah dibayar sebelumnya
        if ($order->payment_status === 'paid') {
            return response()->json(['message' => 'Already paid'], 422);
        }

        // Update status pesanan menjadi paid
        $order->update([
            'payment_status' => 'paid',
            'status' => 'paid',
        ]);

        // Update data pembayaran (status + waktu bayar)
        $order->payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $order->load(['items.variant.product.images', 'payment', 'shipping']);

        return response()->json($order);
    }
}
