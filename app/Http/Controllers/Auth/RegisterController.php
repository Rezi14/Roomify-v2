<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisterController extends Controller
{
    // Menampilkan formulir pendaftaran.
    public function showRegistrationForm(): View
    {
        return view('Auth.register');
    }

    // Menangani proses pendaftaran pengguna baru.
    public function register(Request $request): RedirectResponse
    {
        // Memvalidasi Input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // menentukan ID Role Default (Pelanggan)
        $pelangganRole = Role::firstOrCreate(
            ['nama_role' => 'pelanggan'],
            ['deskripsi' => 'Pengguna biasa yang dapat memesan kamar.']
        );

        // Membuat User Baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_role' => $pelangganRole->id_role,
        ]);

        // Mengirim event registered (berguna jika nanti mengaktifkan verifikasi email)
        event(new Registered($user));

        // Login User Secara Otomatis
        Auth::login($user);

        // Redirect Setelah Pendaftaran Berhasil
        // Mengarahkan ke dashboard pengguna
        return redirect()->intended('/dashboard')
            ->with('success', 'Pendaftaran berhasil! Selamat datang.');
    }
}
