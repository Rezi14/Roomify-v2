<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FasilitasAdminApiController extends Controller
{
    public function index(): JsonResponse
    {
        $fasilitas = Fasilitas::orderBy('id_fasilitas', 'asc')->get();

        return response()->json(['fasilitas' => $fasilitas]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'nama_fasilitas' => ['required', 'string', 'max:255', Rule::unique('fasilitas', 'nama_fasilitas')],
            'deskripsi' => ['nullable', 'string'],
            'biaya_tambahan' => ['nullable', 'numeric', 'min:0'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $fasilitas = Fasilitas::create($validatedData);

            return response()->json([
                'message' => 'Fasilitas berhasil ditambahkan!',
                'fasilitas' => $fasilitas,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan fasilitas: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Fasilitas $fasilitas): JsonResponse
    {
        return response()->json(['fasilitas' => $fasilitas]);
    }

    public function update(Request $request, Fasilitas $fasilitas): JsonResponse
    {
        $validatedData = $request->validate([
            'nama_fasilitas' => [
                'required', 'string', 'max:255',
                Rule::unique('fasilitas', 'nama_fasilitas')->ignore($fasilitas->id_fasilitas, 'id_fasilitas'),
            ],
            'deskripsi' => ['nullable', 'string'],
            'biaya_tambahan' => ['nullable', 'numeric', 'min:0'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $fasilitas->update($validatedData);

            return response()->json([
                'message' => 'Fasilitas berhasil diperbarui!',
                'fasilitas' => $fasilitas->fresh(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui fasilitas: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Fasilitas $fasilitas): JsonResponse
    {
        try {
            $fasilitas->tipeKamars()->detach();
            $fasilitas->pemesanans()->detach();
            $fasilitas->delete();

            return response()->json([
                'message' => 'Fasilitas berhasil dihapus!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus fasilitas: ' . $e->getMessage(),
            ], 500);
        }
    }
}
