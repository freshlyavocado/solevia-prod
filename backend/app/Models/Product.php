<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Model Product — merepresentasikan tabel 'products' di database.
 *
 * Model ini adalah inti dari aplikasi e-commerce Solevia.
 * Menyimpan data produk sepatu termasuk nama, harga, deskripsi, dan slug untuk URL.
 *
 * Relasi:
 * - belongsTo Category  → Setiap produk masuk ke 1 kategori (Sneakers, Running, dll).
 * - belongsTo Brand     → Setiap produk dimiliki oleh 1 brand (Nike, Adidas, dll).
 * - hasMany   Variant   → Setiap produk memiliki banyak varian (ukuran & stok).
 * - hasMany   Image     → Setiap produk bisa memiliki banyak gambar.
 * - hasMany   Wishlist  → Produk bisa di-wishlist oleh banyak user.
 */
class Product extends Model
{
    /**
     * Kolom yang boleh diisi secara mass-assignment.
     * Ini mencegah vulnerability mass-assignment attack.
     */
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'discount_price', 'category_id', 'brand_id',
    ];

    /**
     * Casting otomatis: mengubah tipe data kolom saat diakses.
     * - price          → decimal 2 angka di belakang koma (misal: 1500000.00).
     * - discount_price → decimal 2 angka di belakang koma.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    /**
     * Event Lifecycle: otomatis dijalankan saat produk baru dibuat (creating).
     * Jika slug belum diisi, maka slug akan di-generate dari nama produk.
     * Contoh: "Air Max 90" → "air-max-90"
     */
    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Relasi: Produk dimiliki oleh 1 kategori.
     * Foreign key: category_id di tabel products.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi: Produk dimiliki oleh 1 brand.
     * Foreign key: brand_id di tabel products.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Relasi: Produk memiliki banyak varian (ukuran dan stok).
     * Contoh: Air Max 90 → size 38 (stok 10), size 39 (stok 15), dll.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Relasi: Produk memiliki banyak gambar.
     * Gambar disimpan di storage/app/public/products/.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Relasi: Produk bisa di-wishlist oleh banyak user.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }
}
