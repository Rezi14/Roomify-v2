@extends('layouts.admin.app')

@section('title', 'Manajemen Kamar - Roomify Admin')

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
            <h2 class="mb-0">Manajemen Kamar</h2>
            <a href="{{ route('admin.kamars.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Tambah Kamar Baru
            </a>
        </div>

        {{-- Tabel Daftar Kamar --}}
        <div class="card p-4 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Nomor Kamar</th>
                                <th>Tipe Kamar</th>
                                <th>Harga Per Malam</th>
                                <th>Status</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kamars as $kamar)
                                <tr>
                                    <td>{{ $kamar->nomor_kamar }}</td>
                                    <td>{{ $kamar->tipeKamar->nama_tipe_kamar ?? 'N/A' }}</td>
                                    <td>Rp {{ number_format($kamar->tipeKamar->harga_per_malam ?? 0, 2, ',', '.') }}</td>
                                    <td>
                                        @if ($kamar->status_kamar)
                                            <span class="badge bg-success">Tersedia</span>
                                        @else
                                            <span class="badge bg-danger">Tidak Tersedia</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-grid gap-1">
                                            <a href="{{ route('admin.kamars.edit', $kamar->id_kamar) }}"
                                                class="btn btn-sm btn-warning w-100" title="Edit Kamar">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>

                                            {{-- Tombol Hapus --}}
                                            <form action="{{ route('admin.kamars.destroy', $kamar->id_kamar) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus kamar ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger w-100"
                                                    title="Hapus Kamar">
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
                                            <i class="fas fa-info-circle me-2"></i> Belum Memiliki kamar
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
