<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ProductVariant — merepresentasikan tabel 'product_variants' di database.
 *
 * Menyimpan varian produk berdasarkan ukuran (size) dan stok yang tersedia.
 * Satu produk bisa memiliki banyak varian (misal: size 38-44 dengan stok masing-masing).
 *
 * Contoh data:
 * - product_id: 1 (Air Max 90), size: "42", stock: 25
 *
 * Relasi:
 * - belongsTo Product → Setiap varian milik 1 produk.
 */
class ProductVariant extends Model
{
    /**
     * Kolom yang boleh diisi: product_id, size (ukuran sepatu), stock (jumlah stok).
     */
    protected $fillable = ['product_id', 'size', 'stock'];

    /**
     * Relasi: Varian ini milik 1 produk.
     * Digunakan untuk mengambil data produk dari varian (misal saat checkout).
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
