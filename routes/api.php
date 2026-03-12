<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthApiController;
use App\Http\Controllers\Api\User\kamarApiController;
use App\Http\Controllers\Api\User\BookingApiController;
use App\Http\Controllers\Api\User\ProfileApiController;
use App\Http\Controllers\Api\Admin\DashboardAdminApiController;
use App\Http\Controllers\Api\Admin\KamarAdminApiController;
use App\Http\Controllers\Api\Admin\TipeKamarAdminApiController;
use App\Http\Controllers\Api\Admin\PemesananAdminApiController;
use App\Http\Controllers\Api\Admin\UserAdminApiController;
use App\Http\Controllers\Api\Admin\FasilitasAdminApiController;
/*
|--------------------------------------------------------------------------
| API Routes - Roomify v2
|--------------------------------------------------------------------------
|
| Semua route di sini otomatis mendapatkan prefix /api
| Contoh: POST /api/login, GET /api/kamars, dst.
|
*/

// =====================================================
// GUEST ROUTES (Tidak perlu login)
// =====================================================
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthApiController::class, 'resetPassword']);

// =====================================================
// AUTH ROUTES (Wajib Login via Sanctum Token)
// =====================================================
Route::middleware('auth:sanctum')->group(function () {

    // --- Auth ---
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/user', [AuthApiController::class, 'user']);

    // --- Dashboard User: Cari & Filter Kamar ---
    Route::get('/kamars', [KamarApiController::class, 'index']);
    Route::get('/kamars/{kamar}', [KamarApiController::class, 'show']);
    Route::get('/tipe-kamars', [KamarApiController::class, 'tipeKamars']);
    Route::get('/fasilitas', [KamarApiController::class, 'fasilitas']);

    // --- Booking (User) ---
    Route::get('/booking/form/{kamar}', [BookingApiController::class, 'showBookingForm']);
    Route::post('/booking', [BookingApiController::class, 'store']);
    Route::get('/booking/{id}/detail', [BookingApiController::class, 'detail']);
    Route::get('/booking/{id}/payment', [BookingApiController::class, 'showPayment']);
    Route::get('/booking/{id}/payment/check', [BookingApiController::class, 'checkPaymentStatus']);
    Route::post('/booking/{id}/cancel', [BookingApiController::class, 'cancelBooking']);

    // --- Profile ---
    Route::get('/profile', [ProfileApiController::class, 'index']);
    Route::put('/profile', [ProfileApiController::class, 'update']);
    Route::put('/profile/password', [ProfileApiController::class, 'updatePassword']);
    Route::get('/profile/orders', [ProfileApiController::class, 'orders']);
    Route::get('/profile/orders/{id}', [ProfileApiController::class, 'orderDetail']);

    // =====================================================
    // ADMIN ROUTES (Wajib role admin)
    // =====================================================
    Route::middleware('role:admin')->prefix('admin')->group(function () {

        // Dashboard Admin
        Route::get('/dashboard', [DashboardAdminApiController::class, 'index']);

        // CRUD Kamar
        Route::apiResource('kamars', KamarAdminApiController::class)
            ->parameters(['kamars' => 'kamar']);

        // CRUD Tipe Kamar
        Route::apiResource('tipe-kamars', TipeKamarAdminApiController::class)
            ->parameters(['tipe-kamars' => 'tipeKamar']);

        // CRUD Users
        Route::apiResource('users', UserAdminApiController::class);

        // CRUD Fasilitas
        Route::apiResource('fasilitas', FasilitasAdminApiController::class)
            ->parameters(['fasilitas' => 'fasilitas']);

        // CRUD Pemesanan
        Route::apiResource('pemesanans', PemesananAdminApiController::class)
            ->parameters(['pemesanans' => 'pemesanan']);

        // Aksi khusus pemesanan (sesuai web.php)
        Route::patch('/pemesanans/{pemesanan}/confirm', [PemesananAdminApiController::class, 'confirm']);
        Route::patch('/pemesanans/{pemesanan}/checkin', [PemesananAdminApiController::class, 'checkIn']);
        Route::patch('/pemesanans/{pemesanan}/checkout', [PemesananAdminApiController::class, 'checkout']);

        // Riwayat
        Route::get('/riwayat/pemesanan', [PemesananAdminApiController::class, 'riwayat']);
        Route::get('/riwayat/pemesanan/{id}', [PemesananAdminApiController::class, 'detailRiwayat']);
    });
});
