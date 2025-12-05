@extends('layouts.admin.app')

@section('title', 'Tambah Kamar Baru - Roomify Admin')

@section('content')
    <div class="container-fluid p-4">
        {{-- Header Halaman --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Tambah Kamar Baru</h2>
            <a href="{{ route('admin.kamars.index') }}" class="btn bg-danger text-white">
                <i class="fas fa-arrow-left me-2 "></i> Kembali ke Daftar Kamar
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

                {{-- Form Tambah Kamar --}}
                <form action="{{ route('admin.kamars.store') }}" method="POST">
                    @csrf

                    {{-- Input Nomor Kamar --}}
                    <div class="mb-3">
                        <label for="nomor_kamar" class="form-label">Nomor Kamar</label>
                        <input type="text" class="form-control" id="nomor_kamar" name="nomor_kamar"
                            value="{{ old('nomor_kamar') }}" required>
                    </div>

                    {{-- Select Tipe Kamar --}}
                    <div class="mb-3">
                        <label for="id_tipe_kamar" class="form-label">Tipe Kamar</label>
                        <select class="form-select" id="id_tipe_kamar" name="id_tipe_kamar" required>
                            <option value="">Pilih Tipe Kamar</option>
                            @foreach ($tipeKamars as $tipe)
                                <option value="{{ $tipe->id_tipe_kamar }}"
                                    {{ old('id_tipe_kamar') == $tipe->id_tipe_kamar ? 'selected' : '' }}>
                                    {{ $tipe->nama_tipe_kamar }} (Rp {{ number_format($tipe->harga_per_malam) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Switch Status Kamar --}}
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status_kamar" name="status_kamar" value="1"
                            {{ old('status_kamar', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_kamar">Status Kamar (Tersedia / Tidak Tersedia)</label>
                    </div>

                    {{-- Tombol Simpan --}}
                    <button type="submit" class="btn btn-primary">Simpan Kamar</button>
                </form>
            </div>
        </div>
    </div>
@endsection
