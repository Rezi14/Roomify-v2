@extends('layouts.guest')

@section('title', 'Buat Password Baru')

@section('content')
    <div class="auth-container">
        <div class="auth-header">
            <h2>Password Baru</h2>
            <p>Silakan buat password baru untuk akun Anda.</p>
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

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email (Readonly agar user tidak salah memasukkan email lain) --}}
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control"
                    value="{{ $email ?? old('email') }}" required readonly
                    style="background-color: #f3f4f6; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label for="password">Password Baru</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 8 karakter"
                    required autofocus>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                    placeholder="Ulangi password baru" required>
            </div>

            <button type="submit" class="btn-primary">Simpan Password Baru</button>
        </form>
    </div>
@endsection
