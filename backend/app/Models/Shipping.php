<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Shipping — merepresentasikan tabel 'shippings' di database.
 *
 * Menyimpan informasi pengiriman untuk setiap pesanan.
 * Data ini diisi oleh user saat checkout (nama penerima, alamat, kota, dll).
 *
 * Relasi:
 * - belongsTo Order → Data pengiriman ini untuk 1 pesanan.
 */
class Shipping extends Model
{
    /**
     * Menonaktifkan timestamp otomatis.
     */
    public $timestamps = false;

    /**
     * Kolom yang boleh diisi:
     * - order_id       → ID pesanan.
     * - recipient_name → Nama penerima paket.
     * - phone_number   → Nomor telepon penerima.
     * - address        → Alamat lengkap pengiriman.
     * - city           → Kota tujuan.
     * - province       → Provinsi tujuan.
     * - postal_code    → Kode pos.
     * - shipping_cost  → Ongkos kirim (saat ini hardcoded 0).
     */
    protected $fillable = [
        'order_id', 'recipient_name', 'phone_number', 'address',
        'city', 'province', 'postal_code', 'shipping_cost',
    ];

    /**
     * Casting otomatis:
     * - shipping_cost → decimal 2 angka di belakang koma.
     * - created_at    → objek datetime Carbon.
     */
    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi: Data pengiriman ini untuk 1 pesanan (Order).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
