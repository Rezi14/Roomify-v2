<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Pemesanan;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    // Cari pesanan pending yang dibuat lebih dari 10 menit lalu
    $expiredOrders = Pemesanan::where('status_pemesanan', 'pending')
        ->where('created_at', '<', Carbon::now()->subMinutes(10))
        ->get();

    foreach ($expiredOrders as $order) {
        // Update status pesanan
        $order->update(['status_pemesanan' => 'cancelled']);

        // Kembalikan status kamar jadi tersedia (1)
        if ($order->kamar) {
            $order->kamar->update(['status_kamar' => 1]);
        }
    }
})->everyMinute();
