<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Model User — merepresentasikan tabel 'users' di database.
 *
 * Model ini adalah pusat autentikasi aplikasi Solevia.
 * Menggunakan Laravel Sanctum untuk token-based authentication pada API,
 * dan mengimplementasikan FilamentUser agar bisa mengakses panel admin Filament.
 *
 * Relasi:
 * - hasOne  Cart      → Setiap user memiliki 1 keranjang belanja.
 * - hasMany Order     → Setiap user bisa memiliki banyak pesanan.
 * - hasMany Wishlist  → Setiap user bisa memiliki banyak item wishlist.
 *
 * Attribute Fillable: name, email, password
 * Attribute Hidden  : password, remember_token (tidak ditampilkan di JSON response)
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    // HasApiTokens  → Menyediakan method createToken() untuk Sanctum API auth.
    // HasFactory    → Memungkinkan pembuatan data dummy via factory untuk testing.
    // Notifiable    → Memungkinkan pengiriman notifikasi (email, SMS, dll).
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Menentukan apakah user boleh mengakses panel admin Filament.
     * Saat ini return true = SEMUA user bisa masuk admin panel.
     *
     * ⚠️  WARNING: Di production, ini harus dibatasi!
     *     Contoh: return $this->email === 'admin@solevia.com';
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Mendefinisikan casting otomatis untuk kolom-kolom tertentu.
     *
     * - email_verified_at → diubah otomatis menjadi objek Carbon (datetime).
     * - password          → di-hash otomatis saat di-set (menggunakan bcrypt).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi: User memiliki 1 keranjang belanja (Cart).
     * Tabel carts memiliki kolom 'user_id' sebagai foreign key.
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Relasi: User memiliki banyak pesanan (Order).
     * Digunakan untuk menampilkan riwayat belanja user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relasi: User memiliki banyak item wishlist.
     * Digunakan untuk menyimpan produk favorit user.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }
}
