@extends('layouts.guest')

@section('title', 'Daftar Akun Baru')

@section('content')
    <div class="auth-container">
        <div class="auth-header">
            <h2>Buat Akun Baru</h2>
            <p>Bergabunglah bersama kami sekarang</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Nama Lengkap --}}
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}"
                    placeholder="Nama Lengkap Anda" required autofocus>
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}"
                    placeholder="contoh@email.com" required>
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter"
                    required>
            </div>

            {{-- Konfirmasi Password --}}
            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                    placeholder="Ulangi password Anda" required>
            </div>

            <button type="submit" class="btn-primary">Daftar Sekarang</button>

            <div class="auth-footer">
                Sudah punya akun? <a href="{{ route('login') }}">Login disini</a>
            </div>
        </form>
    </div>
@endsection
