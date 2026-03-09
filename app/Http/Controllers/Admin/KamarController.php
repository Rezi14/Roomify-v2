<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPemesanan;
use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\TipeKamar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KamarController extends Controller
{
    // Menampilkan daftar semua kamar.
    public function index(): View
    {
        // mengambil semua kamar dengan memuat relasi tipeKamar
        $kamars = Kamar::with('tipeKamar')
            ->orderBy('nomor_kamar', 'asc')
            ->get();

        return view('admin.kamars.index', compact('kamars'));
    }

    // Menampilkan formulir untuk membuat kamar baru.
    public function create(): View
    {
        $tipeKamars = TipeKamar::orderBy('id_tipe_kamar', 'asc')->get();

        return view('admin.kamars.create', compact('tipeKamars'));
    }

    // Menyimpan kamar baru ke database.
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'nomor_kamar' => ['required', 'string', 'max:255', Rule::unique('kamars', 'nomor_kamar')],
            'id_tipe_kamar' => ['required', 'exists:tipe_kamars,id_tipe_kamar'],
            'status_kamar' => ['required', 'boolean'],
        ]);

        Kamar::create($validatedData);

        return redirect()->route('admin.kamars.index')
            ->with('success', 'Kamar berhasil ditambahkan!');
    }

    // Menampilkan formulir untuk mengedit kamar.
    public function edit(Kamar $kamar): View
    {
        $tipeKamars = TipeKamar::orderBy('id_tipe_kamar', 'asc')->get();

        return view('admin.kamars.edit', compact('kamar', 'tipeKamars'));
    }

    // Memperbarui kamar di database.
    public function update(Request $request, Kamar $kamar): RedirectResponse
    {
        $validatedData = $request->validate([
            'nomor_kamar' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kamars', 'nomor_kamar')->ignore($kamar->id_kamar, 'id_kamar')
            ],
            'id_tipe_kamar' => ['required', 'exists:tipe_kamars,id_tipe_kamar'],
            'status_kamar' => ['nullable', 'boolean'],
        ]);

        $kamar->update($validatedData);

        return redirect()->route('admin.kamars.index')
            ->with('success', 'Kamar berhasil diperbarui!');
    }

    // Menghapus kamar dari database.
    public function destroy(Kamar $kamar): RedirectResponse
    {
        // Mengecek apakah ada pesanan aktif (pending/confirmed/checkin) untuk kamar ini
        $pesananAktif = $kamar->pemesanans()
            ->whereIn('status_pemesanan', [StatusPemesanan::PENDING, StatusPemesanan::CONFIRMED, StatusPemesanan::CHECKED_IN])
            ->exists();

        if ($pesananAktif) {
            return back()->with('error', 'Kamar tidak bisa dihapus karena sedang dalam proses pemesanan atau digunakan.');
        }

        $kamar->delete();

        return redirect()->route('admin.kamars.index')
            ->with('success', 'Kamar berhasil dihapus!');
    }
}
