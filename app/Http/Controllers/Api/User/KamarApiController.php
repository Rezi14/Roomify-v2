<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusPemesanan;
use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\TipeKamar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KamarApiController extends Controller
{
    /**
     * Memetakan DashboardController@index
     * Menampilkan kamar tersedia dengan filter:
     * - check_in, check_out (cek ketersediaan tanggal)
     * - tipe_kamar (filter tipe)
     * - harga_min, harga_max (filter range harga)
     * - fasilitas[] (filter fasilitas yang harus dimiliki)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Kamar::with(['tipeKamar.fasilitas'])
            ->where('status_kamar', 1)

            // Filter: Tanggal Check-in & Check-out (Cek Ketersediaan)
            ->when($request->filled(['check_in', 'check_out']), function (Builder $query) use ($request) {
                $checkIn  = $request->check_in;
                $checkOut = $request->check_out;

                $query->whereDoesntHave('pemesanans', function (Builder $q) use ($checkIn, $checkOut) {
                    $q->where('status_pemesanan', '!=', StatusPemesanan::CANCELLED)
                      ->where('status_pemesanan', '!=', StatusPemesanan::CHECKED_OUT)
                      ->where(function (Builder $sub) use ($checkIn, $checkOut) {
                          $sub->where('check_in_date', '<', $checkOut)
                              ->where('check_out_date', '>', $checkIn);
                      });
                });
            })

            // Filter: Tipe Kamar
            ->when($request->filled('tipe_kamar'), function (Builder $query) use ($request) {
                $query->where('id_tipe_kamar', $request->tipe_kamar);
            })

            // Filter: Range Harga
            ->when($request->filled('harga_min'), function (Builder $query) use ($request) {
                $query->whereHas('tipeKamar', function (Builder $q) use ($request) {
                    $q->where('harga_per_malam', '>=', $request->harga_min);
                });
            })
            ->when($request->filled('harga_max'), function (Builder $query) use ($request) {
                $query->whereHas('tipeKamar', function (Builder $q) use ($request) {
                    $q->where('harga_per_malam', '<=', $request->harga_max);
                });
            })

            // Filter: Fasilitas (Harus memiliki SEMUA fasilitas yang dipilih)
            ->when($request->filled('fasilitas'), function (Builder $query) use ($request) {
                $fasilitasIds = (array) $request->fasilitas;
                foreach ($fasilitasIds as $fasilitasId) {
                    $query->whereHas('tipeKamar.fasilitas', function (Builder $q) use ($fasilitasId) {
                        $q->where('fasilitas.id_fasilitas', $fasilitasId);
                    });
                }
            });

        $kamars = $query->get();

        return response()->json([
            'message' => 'Daftar kamar tersedia.',
            'kamars' => $kamars,
        ]);
    }

    /**
     * Detail kamar beserta tipe & fasilitas.
     */
    public function show(Kamar $kamar): JsonResponse
    {
        $kamar->load(['tipeKamar.fasilitas']);

        return response()->json([
            'kamar' => $kamar,
            'max_tamu' => $kamar->tipeKamar->kapasitas,
        ]);
    }

    /**
     * Daftar semua tipe kamar (untuk dropdown filter).
     */
    public function tipeKamars(): JsonResponse
    {
        $tipeKamars = TipeKamar::with('fasilitas')->get();

        return response()->json([
            'tipe_kamars' => $tipeKamars,
        ]);
    }

    /**
     * Daftar semua fasilitas (untuk dropdown filter).
     */
    public function fasilitas(): JsonResponse
    {
        $fasilitas = Fasilitas::all();

        return response()->json([
            'fasilitas' => $fasilitas,
        ]);
    }
}
