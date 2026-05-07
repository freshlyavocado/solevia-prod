<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Cart — merepresentasikan tabel 'carts' di database.
 *
 * Keranjang belanja milik seorang user. Setiap user hanya memiliki 1 keranjang.
 * Cart berisi banyak CartItem (item-item yang ditambahkan user ke keranjang).
 *
 * Alur: User menambah produk → Cart dibuat otomatis (firstOrCreate) → CartItem ditambahkan.
 *
 * Relasi:
 * - belongsTo User     → Keranjang milik 1 user.
 * - hasMany   CartItem → Keranjang berisi banyak item.
 */
class Cart extends Model
{
    /**
     * Menonaktifkan timestamp (tabel carts tidak punya created_at/updated_at).
     */
    public $timestamps = false;

    /**
     * Kolom yang boleh diisi: hanya user_id (pemilik keranjang).
     */
    protected $fillable = ['user_id'];

    /**
     * Relasi: Keranjang milik 1 user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Keranjang berisi banyak item (CartItem).
     * Setiap item merujuk ke varian produk tertentu dengan jumlah tertentu.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
