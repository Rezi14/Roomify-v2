<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

// --- Controllers ---
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\User\ContactController;
use App\Http\Controllers\User\ProfileController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;

use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\KamarController;
use App\Http\Controllers\Admin\TipeKamarController;
use App\Http\Controllers\Admin\PemesananController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\FasilitasController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// === ROUTE UMUM ===
// Redirect root ke dashboard user
Route::get('/', fn() => redirect()->route('dashboard'));

// Dashboard User (Named 'dashboard')
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Kontak
Route::controller(ContactController::class)->group(function () {
    Route::get('/kontak', 'index')->name('contact');
    Route::post('/kontak', 'send')->name('contact.send');
});

// === GUEST ROUTES (Login/Register) ===
Route::middleware('guest')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
    });

    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'showRegistrationForm')->name('register');
        Route::post('/register', 'register');
    });
});

// Forgot password
Route::controller(ForgotPasswordController::class)->group(function () {
    Route::get('/forgot-password', 'showLinkRequestForm')->name('password.request');
    Route::post('/forgot-password', 'sendResetLinkEmail')->name('password.email');
});


// === AUTH ROUTES (Wajib Login) ===
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Reset password
    Route::controller(ResetPasswordController::class)->group(function () {
        Route::get('/reset-password/{token}', 'showResetForm')->name('password.reset');
        Route::post('/reset-password', 'reset')->name('password.update');
    });

    // Verifikasi email
    Route::controller(VerificationController::class)->group(function () {
        Route::get('/email/verify', 'show')->name('verification.notice');
        Route::get('/email/verify/{id}/{hash}', 'verify')->middleware('signed')->name('verification.verify');
        Route::post('/email/verification-notification', 'resend')
            ->middleware('throttle:6,1')
            ->name('verification.send');
    });

    // Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile');
        Route::put('/profile/update', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password');
    });

    // Booking (User Verified Only)
    Route::middleware('verified')->controller(BookingController::class)->group(function () {
        Route::get('/pesan-kamar/{kamar}', 'showBookingForm')->name('booking.create');
        Route::post('/pesan-kamar', 'store')->middleware('throttle:5,1')->name('booking.store');
        Route::get('/pesanan/{id}', 'detail')->name('booking.detail');
        Route::get('/pembayaran/{id}', 'showPayment')->name('booking.payment');
        Route::get('/pembayaran/{id}/check', 'checkPaymentStatus')->name('booking.payment.check');
        Route::post('/pembayaran/{id}/cancel', 'cancelBooking')->name('booking.payment.cancel');
        Route::get('/simulasi/qr-scan/{id}', 'simulatePaymentSuccess')->name('simulation.qr.scan');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    // Menggunakan middleware 'admin' yang sesuai dengan RoleMiddleware
    Route::middleware([RoleMiddleware::class . ':admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Route ini menjadi 'admin.dashboard'
            Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');

            // CRUD Resources
            Route::resource('kamars', KamarController::class);
            Route::resource('tipe_kamars', TipeKamarController::class);
            Route::resource('users', UserController::class);
            Route::resource('fasilitas', FasilitasController::class)->parameters([
                'fasilitas' => 'fasilitas'
            ]);

            Route::resource('pemesanans', PemesananController::class);

            // Aksi khusus pemesanan
            Route::controller(PemesananController::class)
                ->prefix('pemesanans/{pemesanan}')
                ->name('pemesanans.')
                ->group(function () {
                Route::patch('/checkin', 'checkIn')->name('checkin');
                Route::patch('/checkout', 'checkout')->name('checkout');
                Route::patch('/confirm', 'confirm')->name('confirm');
            });

            // Riwayat
            Route::get('riwayat/pemesanan', [PemesananController::class, 'riwayat'])->name('riwayat.pemesanan');
            Route::get('riwayat/pemesanan/{id}', [PemesananController::class, 'detailRiwayat'])->name('riwayat.detail');
        });
});
