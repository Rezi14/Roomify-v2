<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FasilitasController extends Controller
{
    // Menampilkan daftar semua fasilitas.
    public function index(): View
    {
        $fasilitas = Fasilitas::orderBy('id_fasilitas', 'asc')->get();

        return view('admin.fasilitas.index', compact('fasilitas'));
    }

    // Menampilkan form tambah fasilitas.
    public function create(): View
    {
        return view('admin.fasilitas.create');
    }

    // Menyimpan fasilitas baru.
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'nama_fasilitas' => ['required', 'string', 'max:255', Rule::unique('fasilitas', 'nama_fasilitas')],
            'deskripsi' => ['nullable', 'string'],
            'biaya_tambahan' => ['nullable', 'numeric', 'min:0'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            Fasilitas::create($validatedData);

            return redirect()->route('admin.fasilitas.index')
                ->with('success', 'Fasilitas berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan fasilitas: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Menampilkan detail fasilitas tertentu.
    public function show(Fasilitas $fasilitas): View
    {
        return view('admin.fasilitas.show', compact('fasilitas'));
    }

    // Menampilkan form edit fasilitas.
    public function edit(Fasilitas $fasilitas): View
    {
        return view('admin.fasilitas.edit', compact('fasilitas'));
    }

    // Memperbarui data fasilitas.
    public function update(Request $request, Fasilitas $fasilitas): RedirectResponse
    {
        $validatedData = $request->validate([
            'nama_fasilitas' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fasilitas', 'nama_fasilitas')->ignore($fasilitas->id_fasilitas, 'id_fasilitas'),
            ],
            'deskripsi' => ['nullable', 'string'],
            'biaya_tambahan' => ['nullable', 'numeric', 'min:0'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $fasilitas->update($validatedData);

            return redirect()->route('admin.fasilitas.index')
                ->with('success', 'Fasilitas berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui fasilitas: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Menghapus fasilitas dari database.
    public function destroy(Fasilitas $fasilitas): RedirectResponse
    {
        try {
            $fasilitas->delete();

            return redirect()->route('admin.fasilitas.index')
                ->with('success', 'Fasilitas berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus fasilitas: ' . $e->getMessage());
        }
    }
}
