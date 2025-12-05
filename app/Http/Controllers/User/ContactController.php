<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Menampilkan halaman kontak.
     */
    public function index(): View
    {
        return view('user.pages.contact');
    }

    /**
     * Memproses pengiriman pesan kontak.
     */
    public function send(Request $request): RedirectResponse
    {
        // 1. Validasi Input
        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        // 2. Logika Kirim Email / Simpan ke Database
        // Di sini Anda bisa menambahkan kode Mail::to(...) atau menyimpan ke tabel pesan.
        // Contoh: Message::create($request->all());

        return back()->with('success', 'Terima kasih! Pesan Anda telah kami terima. Tim kami akan segera menghubungi Anda.');
    }
}
