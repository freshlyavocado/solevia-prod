<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * AuthController — Menangani autentikasi pengguna via API.
 *
 * Controller ini menyediakan endpoint untuk:
 * - Register  → Membuat akun baru dan mendapatkan token.
 * - Login     → Masuk dengan email & password, mendapatkan token.
 * - Logout    → Menghapus token yang sedang digunakan.
 * - User      → Mendapatkan data profil user yang sedang login.
 *
 * Menggunakan Laravel Sanctum untuk token-based authentication.
 * Token dikirim via header: Authorization: Bearer <token>
 */
class AuthController extends Controller
{
    /**
     * Register — Membuat akun user baru.
     *
     * Endpoint: POST /api/register
     *
     * Body yang dibutuhkan:
     * - name                  → Nama lengkap (string, wajib, maks 255 karakter)
     * - email                 → Email (string, wajib, harus unik di tabel users)
     * - password              → Password (string, wajib, minimal 8 karakter)
     * - password_confirmation → Konfirmasi password (harus sama dengan password)
     *
     * Response (201 Created):
     * - user  → Data user yang baru dibuat
     * - token → Token API untuk autentikasi selanjutnya
     */
    public function register(Request $request): JsonResponse
    {
        // Validasi input dari request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Buat user baru di database dengan password yang di-hash
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Generate token Sanctum untuk user baru (agar langsung login setelah register)
        $token = $user->createToken('auth-token')->plainTextToken;

        // Kirim response berisi data user dan token
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login — Masuk ke akun dan mendapatkan token.
     *
     * Endpoint: POST /api/login
     *
     * Body yang dibutuhkan:
     * - email    → Email yang terdaftar
     * - password → Password akun
     *
     * Response (200 OK):
     * - user  → Data user yang login
     * - token → Token API baru
     *
     * Error (422): Jika email/password salah.
     */
    public function login(Request $request): JsonResponse
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Coba autentikasi dengan email dan password
        // Auth::attempt() akan mengecek ke database dan mencocokkan hash password
        if (!Auth::attempt($request->only('email', 'password'))) {
            // Jika gagal, lempar error validasi
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Ambil data user dan buat token baru
        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout — Menghapus token yang sedang digunakan.
     *
     * Endpoint: POST /api/logout (Protected — butuh Bearer Token)
     *
     * Menghapus token yang digunakan di request ini saja.
     * Token lain milik user yang sama tetap aktif.
     */
    public function logout(Request $request): JsonResponse
    {
        // Hapus token yang digunakan saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    /**
     * User — Mendapatkan data profil user yang sedang login.
     *
     * Endpoint: GET /api/user (Protected — butuh Bearer Token)
     *
     * Response: Data user (name, email, dll). Password otomatis di-hidden.
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
