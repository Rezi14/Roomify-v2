<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipeKamar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class TipeKamarAdminApiController extends Controller
{
    public function index(): JsonResponse
    {
        $tipeKamars = TipeKamar::with('fasilitas')
            ->orderBy('id_tipe_kamar', 'asc')
            ->get();

        return response()->json(['tipe_kamars' => $tipeKamars]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'nama_tipe_kamar' => ['required', 'string', 'max:255', Rule::unique('tipe_kamars', 'nama_tipe_kamar')],
            'harga_per_malam' => ['required', 'numeric', 'min:0'],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'fasilitas_ids' => ['nullable', 'array'],
            'fasilitas_ids.*' => ['exists:fasilitas,id_fasilitas'],
        ]);

        // Upload gambar (sama dengan TipeKamarController)
        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('img'), $imageName);
            $validatedData['foto_url'] = '/img/' . $imageName;
        }

        unset($validatedData['foto'], $validatedData['fasilitas_ids']);

        $tipeKamar = TipeKamar::create($validatedData);

        // Sync fasilitas jika ada
        if ($request->has('fasilitas_ids')) {
            $tipeKamar->fasilitas()->sync($request->fasilitas_ids);
        }

        $tipeKamar->load('fasilitas');

        return response()->json([
            'message' => 'Tipe kamar berhasil ditambahkan!',
            'tipe_kamar' => $tipeKamar,
        ], 201);
    }

    public function show(TipeKamar $tipeKamar): JsonResponse
    {
        $tipeKamar->load(['fasilitas', 'kamars']);

        return response()->json(['tipe_kamar' => $tipeKamar]);
    }

    public function update(Request $request, TipeKamar $tipeKamar): JsonResponse
    {
        $validatedData = $request->validate([
            'nama_tipe_kamar' => [
                'required', 'string', 'max:255',
                Rule::unique('tipe_kamars', 'nama_tipe_kamar')->ignore($tipeKamar->id_tipe_kamar, 'id_tipe_kamar'),
            ],
            'harga_per_malam' => ['required', 'numeric', 'min:0'],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'fasilitas_ids' => ['nullable', 'array'],
            'fasilitas_ids.*' => ['exists:fasilitas,id_fasilitas'],
        ]);

        // Upload gambar baru (hapus yang lama, sama dengan TipeKamarController)
        if ($request->hasFile('foto')) {
            if ($tipeKamar->foto_url && File::exists(public_path($tipeKamar->foto_url))) {
                File::delete(public_path($tipeKamar->foto_url));
            }
            $image = $request->file('foto');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('img'), $imageName);
            $validatedData['foto_url'] = '/img/' . $imageName;
        }

        unset($validatedData['foto'], $validatedData['fasilitas_ids']);

        $tipeKamar->update($validatedData);

        if ($request->has('fasilitas_ids')) {
            $tipeKamar->fasilitas()->sync($request->fasilitas_ids);
        }

        return response()->json([
            'message' => 'Tipe kamar berhasil diperbarui!',
            'tipe_kamar' => $tipeKamar->fresh()->load('fasilitas'),
        ]);
    }

    public function destroy(TipeKamar $tipeKamar): JsonResponse
    {
        // Hapus foto jika ada
        if ($tipeKamar->foto_url && File::exists(public_path($tipeKamar->foto_url))) {
            File::delete(public_path($tipeKamar->foto_url));
        }

        $tipeKamar->fasilitas()->detach();
        $tipeKamar->delete();

        return response()->json([
            'message' => 'Tipe kamar berhasil dihapus!',
        ]);
    }
}
