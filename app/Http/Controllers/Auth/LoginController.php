<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('Auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email_or_name' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = $request->input('email_or_name');
        $password = $request->input('password');

        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (Auth::attempt([$fieldType => $loginField, 'password' => $password], $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Mengecek role admin dengan nama role yang konsisten
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Selamat datang, Admin!');
            }

            return redirect()->route('dashboard')
                ->with('success', 'Berhasil login! Selamat datang.');
        }

        return back()->withErrors([
            'email_or_name' => 'Kombinasi email/nama pengguna dan password tidak valid.',
        ])->onlyInput('email_or_name');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Anda telah logout.');
    }
}
