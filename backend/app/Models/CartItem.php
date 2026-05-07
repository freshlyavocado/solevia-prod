<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model CartItem — merepresentasikan tabel 'cart_items' di database.
 *
 * Menyimpan item individual di dalam keranjang belanja.
 * Setiap CartItem merujuk ke 1 varian produk (ProductVariant) dengan jumlah (quantity) tertentu.
 *
 * Contoh: CartItem → variant_id: 5 (Air Max 90 size 42), quantity: 2
 *
 * Relasi:
 * - belongsTo Cart           → Item ini ada di dalam 1 keranjang.
 * - belongsTo ProductVariant → Item merujuk ke 1 varian produk (size + stok tertentu).
 */
class CartItem extends Model
{
    /**
     * Menonaktifkan timestamp otomatis.
     */
    public $timestamps = false;

    /**
     * Kolom yang boleh diisi:
     * - cart_id    → ID keranjang pemilik item ini.
     * - variant_id → ID varian produk yang dipilih.
     * - quantity   → Jumlah item yang dimasukkan ke keranjang.
     */
    protected $fillable = ['cart_id', 'variant_id', 'quantity'];

    /**
     * Casting: kolom created_at diubah menjadi objek datetime Carbon.
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relasi: Item ini ada di dalam 1 keranjang (Cart).
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Relasi: Item merujuk ke 1 varian produk.
     * Menggunakan 'variant_id' sebagai foreign key (bukan default 'product_variant_id').
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
