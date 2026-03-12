<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\StatusPemesanan;
use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardAdminApiController extends Controller
{
    /**
     * Memetakan DashboardAdminController@index
     * Statistik + pelanggan yang sedang check-in.
     */
    public function index(): JsonResponse
    {
        $totalKamar = Kamar::count();
        $totalPemesanan = Pemesanan::count();
        $totalPengguna = User::where('id_role', '2')->count();

        $pelangganCheckin = Pemesanan::with(['user', 'kamar.tipeKamar', 'fasilitas'])
            ->where('status_pemesanan', StatusPemesanan::CHECKED_IN)
            ->orderBy('id_pemesanan', 'desc')
            ->get();

        return response()->json([
            'total_kamar' => $totalKamar,
            'total_pemesanan' => $totalPemesanan,
            'total_pengguna' => $totalPengguna,
            'pelanggan_checkin' => $pelangganCheckin,
        ]);
    }
}
