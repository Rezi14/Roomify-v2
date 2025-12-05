<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('user.pages.profile', compact('user'));
    }

    /**
     * Mengupdate Nama dan Email.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->update($validatedData);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Mengganti Password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        // Cek apakah password lama benar
        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini salah!'])
                ->withInput(); // Kembalikan input agar user tidak perlu mengetik ulang (kecuali password)
        }

        // Update password baru
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}
