<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\StatusPemesanan;
use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\TipeKamar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KamarAdminApiController extends Controller
{
    public function index(): JsonResponse
    {
        $kamars = Kamar::with('tipeKamar')
            ->orderBy('nomor_kamar', 'asc')
            ->get();

        return response()->json(['kamars' => $kamars]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'nomor_kamar' => ['required', 'string', 'max:255', Rule::unique('kamars', 'nomor_kamar')],
            'id_tipe_kamar' => ['required', 'exists:tipe_kamars,id_tipe_kamar'],
            'status_kamar' => ['required', 'boolean'],
        ]);

        $kamar = Kamar::create($validatedData);
        $kamar->load('tipeKamar');

        return response()->json([
            'message' => 'Kamar berhasil ditambahkan!',
            'kamar' => $kamar,
        ], 201);
    }

    public function show(Kamar $kamar): JsonResponse
    {
        $kamar->load('tipeKamar');

        return response()->json(['kamar' => $kamar]);
    }

    public function update(Request $request, Kamar $kamar): JsonResponse
    {
        $validatedData = $request->validate([
            'nomor_kamar' => [
                'required', 'string', 'max:255',
                Rule::unique('kamars', 'nomor_kamar')->ignore($kamar->id_kamar, 'id_kamar'),
            ],
            'id_tipe_kamar' => ['required', 'exists:tipe_kamars,id_tipe_kamar'],
            'status_kamar' => ['nullable', 'boolean'],
        ]);

        $kamar->update($validatedData);

        return response()->json([
            'message' => 'Kamar berhasil diperbarui!',
            'kamar' => $kamar->fresh()->load('tipeKamar'),
        ]);
    }

    public function destroy(Kamar $kamar): JsonResponse
    {
        // Cek pesanan aktif (sama dengan KamarController@destroy)
        $pesananAktif = $kamar->pemesanans()
            ->whereIn('status_pemesanan', [
                StatusPemesanan::PENDING,
                StatusPemesanan::CONFIRMED,
                StatusPemesanan::CHECKED_IN,
            ])
            ->exists();

        if ($pesananAktif) {
            return response()->json([
                'message' => 'Kamar tidak bisa dihapus karena masih memiliki pesanan aktif.',
            ], 422);
        }

        $kamar->delete();

        return response()->json([
            'message' => 'Kamar berhasil dihapus!',
        ]);
    }
}
