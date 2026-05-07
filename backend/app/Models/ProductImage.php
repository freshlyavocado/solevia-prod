<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ProductImage — merepresentasikan tabel 'product_images' di database.
 *
 * Menyimpan URL/path gambar produk. Satu produk bisa memiliki beberapa gambar.
 * Gambar disimpan di disk 'public' (storage/app/public/products/).
 *
 * Catatan: timestamps dinonaktifkan ($timestamps = false)
 * karena tabel ini tidak memiliki kolom created_at / updated_at.
 *
 * Relasi:
 * - belongsTo Product → Setiap gambar milik 1 produk.
 */
class ProductImage extends Model
{
    /**
     * Menonaktifkan timestamp otomatis (created_at, updated_at).
     * Tabel product_images tidak memiliki kolom tersebut.
     */
    public $timestamps = false;

    /**
     * Kolom yang boleh diisi: product_id dan image_url (path ke file gambar).
     */
    protected $fillable = ['product_id', 'image_url'];

    /**
     * Relasi: Gambar ini milik 1 produk.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
