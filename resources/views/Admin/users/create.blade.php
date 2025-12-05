@extends('layouts.admin.app')

@section('title', 'Tambah Pengguna - Roomify Admin')

@section('content')
    <div class="container-fluid px-4">
        <h2 class="mt-4">Tambah Pengguna Baru</h2>
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('admin.users.index') }}" class="btn btn-danger text-white">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user-plus me-1"></i>
                Formulir Pengguna
            </div>
            <div class="card-body">
                {{-- Tampilkan Error Validasi --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                            required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="id_role" class="form-label">Role (Peran)</label>
                        <select class="form-select" id="id_role" name="id_role" required>
                            <option value="" selected disabled>-- Pilih Role --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id_role }}"
                                    {{ old('id_role') == $role->id_role ? 'selected' : '' }}>
                                    {{ ucfirst($role->nama_role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
