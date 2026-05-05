<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipping extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'recipient_name', 'phone_number', 'address',
        'city', 'province', 'postal_code', 'shipping_cost',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
