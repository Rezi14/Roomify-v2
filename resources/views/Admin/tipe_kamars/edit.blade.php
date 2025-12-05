@extends('layouts.admin.app')

@section('title', 'Edit Tipe Kamar - Roomify Admin')

@section('content')
    <div class="container-fluid px-4">
        {{-- Header Halaman --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Edit Tipe Kamar: {{ $tipeKamar->nama_tipe_kamar }}</h2>
            <a href="{{ route('admin.tipe_kamars.index') }}" class="btn bg-danger text-white">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Tipe Kamar
            </a>
        </div>

        {{-- Card Form --}}
        <div class="card p-4 shadow-sm">
            <div class="card-body">
                {{-- Alert Error --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form Edit Tipe Kamar --}}
                <form action="{{ route('admin.tipe_kamars.update', $tipeKamar->id_tipe_kamar) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama_tipe_kamar" class="form-label">Nama Tipe Kamar</label>
                        <input type="text" class="form-control" id="nama_tipe_kamar" name="nama_tipe_kamar"
                            value="{{ old('nama_tipe_kamar', $tipeKamar->nama_tipe_kamar) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="harga_per_malam" class="form-label">Harga Per Malam</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" step="0.01" class="form-control" id="harga_per_malam"
                                name="harga_per_malam" value="{{ old('harga_per_malam', $tipeKamar->harga_per_malam) }}"
                                required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="kapasitas" class="form-label">Kapasitas (Orang)</label>
                        <input type="number" class="form-control" id="kapasitas" name="kapasitas"
                            value="{{ old('kapasitas', $tipeKamar->kapasitas) }}" required min="1">
                        <div class="form-text">Jumlah maksimal orang yang diperbolehkan dalam satu kamar.</div>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $tipeKamar->deskripsi) }}</textarea>
                    </div>

                    {{-- Input Upload Foto --}}
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto Kamar</label>

                        {{-- Tampilkan foto saat ini jika ada --}}
                        @if ($tipeKamar->foto_url)
                            <div class="mb-2">
                                <img src="{{ asset($tipeKamar->foto_url) }}" alt="Foto Saat Ini" class="img-thumbnail"
                                    style="max-height: 150px;">
                                <div class="form-text">Foto saat ini. Biarkan kosong jika tidak ingin mengubah.</div>
                            </div>
                        @endif

                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                        <div class="form-text">Format: JPG, JPEG, PNG. Maksimal 2MB.</div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Perbarui Tipe Kamar
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
