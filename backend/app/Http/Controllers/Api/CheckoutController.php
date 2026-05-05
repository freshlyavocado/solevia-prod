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

class CheckoutController extends Controller
{
    public function store(Request $request): JsonResponse
    {
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
        $cart = Cart::where('user_id', $user->id)->with('items.variant.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 422);
        }

        return DB::transaction(function () use ($validated, $user, $cart) {
            // Calculate total
            $totalAmount = 0;
            foreach ($cart->items as $item) {
                $totalAmount += $item->variant->product->price * $item->quantity;
            }

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            // Create order items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'item_price' => $item->variant->product->price,
                ]);

                // Reduce stock
                $item->variant->decrement('stock', $item->quantity);
            }

            // Create payment
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $validated['payment_method'],
                'amount' => $totalAmount,
                'status' => 'pending',
            ]);

            // Create shipping
            Shipping::create([
                'order_id' => $order->id,
                'recipient_name' => $validated['recipient_name'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'postal_code' => $validated['postal_code'],
                'shipping_cost' => 0,
                'created_at' => now(),
            ]);

            // Clear cart
            $cart->items()->delete();

            $order->load(['items.variant.product', 'payment', 'shipping']);

            return response()->json($order, 201);
        });
    }
}
