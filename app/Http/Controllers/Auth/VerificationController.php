<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    // Tampilkan pemberitahuan verifikasi email.
    public function show(): View
    {
        return view('Auth.verify-email');
    }

    // Proses verifikasi ketika user klik link di email.
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect('/dashboard')->with('success', 'Email berhasil diverifikasi!');
    }

    // Kirim ulang email verifikasi.
    public function resend(Request $request): RedirectResponse
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Link verifikasi telah dikirim ulang ke email Anda!');
    }
}
