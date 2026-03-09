<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPemesanan;
use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Models\User;

class DashboardAdminController extends Controller
{
    // Menampilkan halaman dashboard admin beserta ringkasan data.
    public function index()
    {
        // Mengambil Data Statistik
        $totalKamar = Kamar::count();
        $totalPemesanan = Pemesanan::count();
        $totalPengguna = User::where('id_role', '2')->count();

        // Mengambil Data Pelanggan yang Sedang Check-in
        $pelangganCheckin = Pemesanan::with(['user', 'kamar.tipeKamar', 'fasilitas'])
            ->where('status_pemesanan', StatusPemesanan::CHECKED_IN)
            ->orderBy('id_pemesanan', 'desc')
            ->get();

        // Return ke View
        return view('Admin.dashboard', compact(
            'totalKamar',
            'totalPemesanan',
            'totalPengguna',
            'pelangganCheckin'
        ));
    }
}
