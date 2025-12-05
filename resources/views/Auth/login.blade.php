@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="auth-container">
        <div class="auth-header">
            <h2>Selamat Datang</h2>
            <p>Silakan masuk ke akun Anda</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email_or_name">Email atau Username</label>
                <input type="text" id="email_or_name" name="email_or_name" class="form-control"
                    value="{{ old('email_or_name') }}" placeholder="Masukkan email/username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password"
                    required>
            </div>

            {{-- Container untuk Kiri-Kanan --}}
            <div class="auth-actions">
                <div class="remember-me">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Ingat Saya</label>
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        Lupa Password?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-primary">Masuk Sekarang</button>

            <div class="auth-footer">
                Belum punya akun? <a href="{{ route('register') }}">Daftar disini</a>
            </div>
        </form>
    </div>
@endsection
