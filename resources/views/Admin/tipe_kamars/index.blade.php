@extends('layouts.admin.app')

@section('title', 'Manajemen Tipe Kamar - Roomify Admin')

@section('content')
    <div class="container-fluid px-4">
        {{-- Alert Pesan Sukses/Error --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Header Halaman --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Manajemen Tipe Kamar</h2>
            <a href="{{ route('admin.tipe_kamars.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Tambah Tipe Kamar Baru
            </a>
        </div>

        {{-- Tabel Daftar Tipe Kamar --}}
        <div class="card p-4 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Nama Tipe</th>
                                <th>Harga Per Malam</th>
                                <th>Deskripsi</th>
                                <th>Foto</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tipeKamars as $tipeKamar)
                                <tr>
                                    <td>{{ $tipeKamar->nama_tipe_kamar }}</td>
                                    <td>Rp {{ number_format($tipeKamar->harga_per_malam, 2, ',', '.') }}</td>
                                    <td>{{ Str::limit($tipeKamar->deskripsi, 50) }}</td>
                                    <td>
                                        @if ($tipeKamar->foto_url)
                                            <a href="{{ asset($tipeKamar->foto_url) }}" target="_blank">
                                                <img src="{{ asset($tipeKamar->foto_url) }}"
                                                    alt="Foto {{ $tipeKamar->nama_tipe_kamar }}" class="img-thumbnail"
                                                    style="width: 80px; height: 50px; object-fit: cover;">
                                            </a>
                                        @else
                                            <span class="text-muted">Tidak ada foto</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-grid gap-1">
                                            <a href="{{ route('admin.tipe_kamars.edit', $tipeKamar->id_tipe_kamar) }}"
                                                class="btn btn-sm btn-warning w-100" title="Edit Tipe Kamar">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form
                                                action="{{ route('admin.tipe_kamars.destroy', $tipeKamar->id_tipe_kamar) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus tipe kamar ini? Ini akan juga menghapus kamar yang terkait!');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger w-100"
                                                    title="Hapus Tipe Kamar">
                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i> Belum Ada Tipe Kamar
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
