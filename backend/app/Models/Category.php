<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Category — merepresentasikan tabel 'categories' di database.
 *
 * Menyimpan kategori produk sepatu. Contoh: Sneakers, Running, Formal, Sandals.
 * Digunakan untuk mengelompokkan produk agar mudah dicari dan difilter oleh user.
 *
 * Relasi:
 * - hasMany Product → Satu kategori memiliki banyak produk.
 */
class Category extends Model
{
    /**
     * Menonaktifkan timestamp (tabel categories tidak punya created_at/updated_at).
     */
    public $timestamps = false;

    /**
     * Kolom yang boleh diisi: name (nama kategori) dan description (deskripsi).
     */
    protected $fillable = ['name', 'description'];

    /**
     * Relasi: Kategori memiliki banyak produk.
     * Digunakan di API untuk menampilkan jumlah produk per kategori (withCount).
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
