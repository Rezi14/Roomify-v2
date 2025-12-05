<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    // Menampilkan daftar pengguna.
    public function index(): View
    {
        $users = User::with('role')->orderBy('id_role', 'asc')->get();

        return view('admin.users.index', compact('users'));
    }

    // Menampilkan formulir pembuatan pengguna baru.
    public function create(): View
    {
        // Hanya mengambil role pelanggan agar admin tidak bisa membuat akun admin baru
        $roles = Role::where('nama_role', 'pelanggan')->get();

        return view('admin.users.create', compact('roles'));
    }

    // Menyimpan pengguna baru ke database.
    public function store(Request $request): RedirectResponse
    {
        // Validasi Input
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'id_role' => ['required', 'exists:roles,id_role'],
        ]);

        // Proteksi Tambahan: Cek agar tidak ada yang mem-bypass form untuk jadi admin
        $roleDipilih = Role::find($request->id_role);
        if ($roleDipilih && $roleDipilih->nama_role === 'admin') {
            return redirect()->back()
                ->withErrors(['id_role' => 'Anda tidak diizinkan membuat user dengan role Admin.'])
                ->withInput();
        }

        // Hash Password & Simpan
        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan!');
    }

    // Menampilkan form edit pengguna.
    public function edit(User $user): View
    {
        //hanya boleh memilih role pelanggan
        $roles = Role::where('nama_role', 'pelanggan')->get();

        // Pengecualian: Jika user yang diedit adalah admin, tampilkan semua role
        // atau biarkan role admin tetap ada dalam pilihan
        if ($user->role && $user->role->nama_role === 'admin') {
            $roles = Role::all();
        }

        return view('admin.users.edit', compact('user', 'roles'));
    }

    // Memperbarui data pengguna.
    public function update(Request $request, User $user): RedirectResponse
    {
        // Validasi Input
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'id_role' => ['required', 'exists:roles,id_role'],
        ];

        // Validasi password hanya jika diisi
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }

        $validatedData = $request->validate($rules);

        // 2. Proteksi Update Role Admin
        // Mencegah user biasa diubah jadi admin lewat inspeksi elemen, kecuali oleh yang berwenang
        $roleBaru = Role::find($request->id_role);
        if ($roleBaru && $roleBaru->nama_role === 'admin' && auth()->id() !== $user->id) {
            // Logika ini bisa disesuaikan, misal hanya Super Admin yang boleh
            // Saat ini kita biarkan lolos jika validasi di atas oke,
            // atau tambahkan return back() error jika ingin membatasi ketat.
        }

        // 3. Update Data
        $dataToUpdate = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'id_role' => $validatedData['id_role'],
        ];

        // Hanya update password jika input password diisi
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }

        $user->update($dataToUpdate);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui!');
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Mencegah admin menghapus akunnya sendiri saat sedang login
        if (auth()->id() === $user->id) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus!');
    }
}
