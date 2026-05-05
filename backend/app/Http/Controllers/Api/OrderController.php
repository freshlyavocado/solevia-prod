<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items.variant.product.images', 'payment', 'shipping'])
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with(['items.variant.product.images', 'payment', 'shipping'])
            ->findOrFail($id);

        return response()->json($order);
    }

    public function confirmPayment(Request $request, int $id): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->with('payment')
            ->findOrFail($id);

        if ($order->payment_status === 'paid') {
            return response()->json(['message' => 'Already paid'], 422);
        }

        $order->update([
            'payment_status' => 'paid',
            'status' => 'paid',
        ]);

        $order->payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $order->load(['items.variant.product.images', 'payment', 'shipping']);

        return response()->json($order);
    }
}
