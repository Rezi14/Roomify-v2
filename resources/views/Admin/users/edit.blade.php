@extends('layouts.admin.app')

@section('title', 'Edit Pengguna - Roomify Admin')

@section('content')
    <div class="container-fluid px-4">
        {{-- Header Halaman --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Edit Pengguna: {{ $user->name }}</h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-danger text-white">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Pengguna
            </a>
        </div>

        {{-- Card Form --}}
        <div class="card p-4 shadow-sm">
            <div class="card-body">
                {{-- Alert Error Validasi --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form Edit Pengguna --}}
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Nama Pengguna --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $user->name) }}" required>
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ old('email', $user->email) }}" required>
                    </div>

                    {{-- Password Baru --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>

                    {{-- Peran (Role) --}}
                    <div class="mb-3">
                        <label for="id_role" class="form-label">Peran</label>
                        <select class="form-select" id="id_role" name="id_role" required>
                            <option value="">Pilih Peran</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id_role }}"
                                    {{ old('id_role', $user->id_role) == $role->id_role ? 'selected' : '' }}>
                                    {{ $role->nama_role }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol Simpan --}}
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Perbarui Pengguna
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
