<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Brand — merepresentasikan tabel 'brands' di database.
 *
 * Menyimpan data merek/brand produk sepatu. Contoh: Nike, Adidas, Puma, New Balance.
 * Setiap brand bisa memiliki logo dan deskripsi.
 *
 * Relasi:
 * - hasMany Product → Satu brand memiliki banyak produk.
 */
class Brand extends Model
{
    /**
     * Menonaktifkan timestamp (tabel brands tidak punya created_at/updated_at).
     */
    public $timestamps = false;

    /**
     * Kolom yang boleh diisi:
     * - name        → Nama brand (misal: "Nike")
     * - logo_url    → Path ke file logo brand (disimpan di storage)
     * - description → Deskripsi/tagline brand (misal: "Just Do It")
     */
    protected $fillable = ['name', 'logo_url', 'description'];

    /**
     * Relasi: Brand memiliki banyak produk.
     * Digunakan di API untuk menampilkan jumlah produk per brand.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
