@extends('layouts.guest')

@section('title', 'Verifikasi Email')

@section('content')
    <div class="auth-container">
        <div class="auth-header">
            <h2>Verifikasi Email</h2>
            <p>Terima kasih telah mendaftar! Mohon cek email Anda untuk link verifikasi.</p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success">
                Link verifikasi baru telah dikirim ke alamat email yang Anda gunakan saat mendaftar.
            </div>
        @endif

        <div style="margin-bottom: 20px; font-size: 0.95em; color: #4b5563; line-height: 1.6;">
            Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan mengklik link yang baru saja kami kirimkan?
            Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkannya lagi.
        </div>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary">Kirim Ulang Email Verifikasi</button>
        </form>

        <div class="auth-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    style="background: none; border: none; color: #2563eb; font-weight: 500; cursor: pointer; text-decoration: underline;">
                    Keluar (Logout)
                </button>
            </form>
        </div>
    </div>
@endsection
