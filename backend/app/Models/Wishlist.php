<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Wishlist — merepresentasikan tabel 'wishlists' di database.
 *
 * Menyimpan produk favorit/wishlist milik user.
 * User bisa menambah dan menghapus produk dari wishlist.
 * Satu user hanya bisa menambahkan 1 produk yang sama 1 kali (unique: user_id + product_id).
 *
 * Relasi:
 * - belongsTo User    → Wishlist milik 1 user.
 * - belongsTo Product → Wishlist merujuk ke 1 produk.
 */
class Wishlist extends Model
{
    /**
     * Menonaktifkan timestamp otomatis.
     */
    public $timestamps = false;

    /**
     * Kolom yang boleh diisi: user_id dan product_id.
     */
    protected $fillable = ['user_id', 'product_id'];

    /**
     * Casting: created_at diubah menjadi objek datetime Carbon.
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relasi: Wishlist milik 1 user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Wishlist merujuk ke 1 produk.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
