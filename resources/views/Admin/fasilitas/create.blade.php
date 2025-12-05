@extends('layouts.admin.app')

@section('title', 'Tambah Fasilitas Baru - Roomify Admin')

@section('content')
    <div class="container-fluid px-4">
        {{-- Header Halaman --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Tambah Fasilitas Baru</h2>
            <a href="{{ route('admin.fasilitas.index') }}" class="btn btn-danger text-white">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Fasilitas
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

                {{-- Form Tambah Fasilitas --}}
                <form action="{{ route('admin.fasilitas.store') }}" method="POST">
                    @csrf

                    {{-- Nama Fasilitas --}}
                    <div class="mb-3">
                        <label for="nama_fasilitas" class="form-label">Nama Fasilitas</label>
                        <input type="text" class="form-control" id="nama_fasilitas" name="nama_fasilitas"
                            value="{{ old('nama_fasilitas') }}" required>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                    </div>

                    {{-- Biaya Tambahan --}}
                    <div class="mb-3">
                        <label for="biaya_tambahan" class="form-label">Biaya Tambahan (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="biaya_tambahan" name="biaya_tambahan"
                                step="0.01" value="{{ old('biaya_tambahan') ?? 0 }}">
                        </div>
                        <div class="form-text">Masukkan 0 jika fasilitas ini gratis.</div>
                    </div>

                    {{-- Tombol Simpan --}}
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Fasilitas
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
