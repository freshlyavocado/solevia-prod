<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model OrderItem — merepresentasikan tabel 'order_items' di database.
 *
 * Menyimpan detail item di dalam sebuah pesanan (Order).
 * Berbeda dengan CartItem, OrderItem menyimpan harga saat pembelian (item_price)
 * sehingga meskipun harga produk berubah, harga pesanan tetap sesuai saat checkout.
 *
 * Relasi:
 * - belongsTo Order          → Item ini bagian dari 1 pesanan.
 * - belongsTo ProductVariant → Item merujuk ke 1 varian produk.
 */
class OrderItem extends Model
{
    /**
     * Menonaktifkan timestamp otomatis.
     */
    public $timestamps = false;

    /**
     * Kolom yang boleh diisi:
     * - order_id   → ID pesanan induk.
     * - variant_id → ID varian produk yang dibeli.
     * - quantity   → Jumlah yang dibeli.
     * - item_price → Harga satuan SAAT pembelian (snapshot harga).
     */
    protected $fillable = ['order_id', 'variant_id', 'quantity', 'item_price'];

    /**
     * Casting: item_price diformat sebagai decimal 2 angka di belakang koma.
     */
    protected $casts = [
        'item_price' => 'decimal:2',
    ];

    /**
     * Relasi: Item ini bagian dari 1 pesanan (Order).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi: Item merujuk ke 1 varian produk.
     * Menggunakan 'variant_id' sebagai foreign key kustom.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
