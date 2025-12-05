@extends('layouts.guest')

@section('title', 'Lupa Password')

@section('content')
    <div class="auth-container">
        <div class="auth-header">
            <h2>Reset Password</h2>
            <p>Masukkan email Anda untuk menerima link reset.</p>
        </div>

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

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}"
                    placeholder="Masukkan email yang terdaftar" required autofocus>
            </div>

            <button type="submit" class="btn-primary">Kirim Link Reset</button>

            <div class="auth-footer">
                Ingat password Anda? <a href="{{ route('login') }}">Kembali ke Login</a>
            </div>
        </form>
    </div>
@endsection
