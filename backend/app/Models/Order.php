<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Model Order — merepresentasikan tabel 'orders' di database.
 *
 * Menyimpan data pesanan yang dibuat setelah user melakukan checkout.
 * Setiap order memiliki nomor unik, total harga, status pesanan, dan status pembayaran.
 *
 * Alur: User checkout → Order dibuat → OrderItem dibuat → Payment dibuat → Shipping dibuat.
 *
 * Status Order:
 * - pending    → Menunggu pembayaran
 * - paid       → Sudah dibayar
 * - shipped    → Sedang dikirim
 * - completed  → Selesai
 * - cancelled  → Dibatalkan
 *
 * Status Pembayaran:
 * - unpaid → Belum dibayar
 * - paid   → Sudah dibayar
 *
 * Relasi:
 * - belongsTo User      → Pesanan milik 1 user.
 * - hasMany   OrderItem → Pesanan berisi banyak item produk.
 * - hasOne    Payment   → Pesanan memiliki 1 data pembayaran.
 * - hasOne    Shipping  → Pesanan memiliki 1 data pengiriman.
 */
class Order extends Model
{
    /**
     * Kolom yang boleh diisi:
     * - user_id        → Pemilik pesanan.
     * - order_number   → Nomor pesanan unik (format: ORD-XXXXXXXX).
     * - total_amount   → Total harga pesanan.
     * - status         → Status pesanan (pending/paid/shipped/completed/cancelled).
     * - payment_status → Status pembayaran (unpaid/paid).
     */
    protected $fillable = [
        'user_id', 'order_number', 'total_amount', 'status', 'payment_status',
    ];

    /**
     * Casting: total_amount diformat menjadi decimal 2 angka di belakang koma.
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    /**
     * Relasi: Pesanan milik 1 user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Pesanan berisi banyak item (OrderItem).
     * Setiap item menyimpan varian produk, jumlah, dan harga saat pembelian.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi: Pesanan memiliki 1 data pembayaran (Payment).
     * Menyimpan metode bayar, jumlah, status, dan waktu bayar.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Relasi: Pesanan memiliki 1 data pengiriman (Shipping).
     * Menyimpan nama penerima, alamat, kota, provinsi, kode pos.
     */
    public function shipping(): HasOne
    {
        return $this->hasOne(Shipping::class);
    }
}
