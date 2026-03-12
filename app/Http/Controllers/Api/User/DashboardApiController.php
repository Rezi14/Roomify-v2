<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\StatusPemesanan;
use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\TipeKamar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Kamar::with(['tipeKamar.fasilitas'])
            ->where('status_kamar', 1)
            ->when($request->filled(['check_in', 'check_out']), function (Builder $q) use ($request) {
                $q->whereDoesntHave('pemesanans', function (Builder $sub) use ($request) {
                    $sub->where('status_pemesanan', '!=', StatusPemesanan::CANCELLED)
                        ->where('status_pemesanan', '!=', StatusPemesanan::CHECKED_OUT)
                        ->where(function (Builder $s) use ($request) {
                            $s->where('check_in_date', '<', $request->check_out)
                              ->where('check_out_date', '>', $request->check_in);
                        });
                });
            })
            ->when($request->filled('tipe_kamar'), fn(Builder $q) => $q->where('id_tipe_kamar', $request->tipe_kamar))
            ->when($request->filled('harga_min'), function (Builder $q) use ($request) {
                $q->whereHas('tipeKamar', fn(Builder $s) => $s->where('harga_per_malam', '>=', $request->harga_min));
            })
            ->when($request->filled('harga_max'), function (Builder $q) use ($request) {
                $q->whereHas('tipeKamar', fn(Builder $s) => $s->where('harga_per_malam', '<=', $request->harga_max));
            });

        return response()->json([
            'kamars' => $query->get(),
        ]);
    }

    public function show(Kamar $kamar): JsonResponse
    {
        $kamar->load(['tipeKamar.fasilitas']);

        return response()->json(['kamar' => $kamar]);
    }

    public function tipeKamars(): JsonResponse
    {
        return response()->json(['tipe_kamars' => TipeKamar::all()]);
    }

    public function fasilitas(): JsonResponse
    {
        return response()->json(['fasilitas' => Fasilitas::all()]);
    }
}
