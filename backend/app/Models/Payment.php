<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Payment — merepresentasikan tabel 'payments' di database.
 *
 * Menyimpan informasi pembayaran untuk setiap pesanan.
 * Setiap Order memiliki 1 Payment.
 *
 * Metode pembayaran yang tersedia: 'qris' dan 'cod'.
 *
 * Status pembayaran:
 * - pending → Menunggu pembayaran
 * - paid    → Sudah dibayar (paid_at akan diisi waktu pembayaran)
 *
 * Relasi:
 * - belongsTo Order → Pembayaran ini untuk 1 pesanan.
 */
class Payment extends Model
{
    /**
     * Menonaktifkan timestamp otomatis.
     */
    public $timestamps = false;

    /**
     * Kolom yang boleh diisi:
     * - order_id       → ID pesanan yang dibayar.
     * - payment_method → Metode pembayaran ('qris' atau 'cod').
     * - amount         → Jumlah yang harus dibayar (dalam Rupiah).
     * - status         → Status pembayaran ('pending' atau 'paid').
     * - paid_at        → Waktu pembayaran dilakukan (null jika belum bayar).
     */
    protected $fillable = ['order_id', 'payment_method', 'amount', 'status', 'paid_at'];

    /**
     * Casting otomatis:
     * - amount  → decimal 2 angka di belakang koma.
     * - paid_at → objek datetime Carbon.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Relasi: Pembayaran ini untuk 1 pesanan (Order).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
