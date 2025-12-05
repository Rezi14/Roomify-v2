@extends('layouts.admin.app')

@section('title', 'Manajemen Fasilitas - Roomify Admin')

@section('content')
    <div class="container-fluid px-4">
        {{-- Alert Messages --}}
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
            <h2 class="mb-0">Manajemen Fasilitas</h2>
            <a href="{{ route('admin.fasilitas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Tambah Fasilitas Baru
            </a>
        </div>

        {{-- Tabel Daftar Fasilitas --}}
        <div class="card p-4 shadow-sm">
            <div class="card-body">
                @if ($fasilitas->isEmpty())
                    <div class="alert alert-info text-center mb-0">
                        Tidak ada fasilitas yang terdaftar.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Nama Fasilitas</th>
                                    <th>Deskripsi</th>
                                    <th>Biaya Tambahan</th>
                                    <th style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fasilitas as $item)
                                    <tr>
                                        <td>{{ $item->nama_fasilitas }}</td>
                                        <td>{{ $item->deskripsi ?? '-' }}</td>
                                        <td>Rp {{ number_format($item->biaya_tambahan, 2, ',', '.') }}</td>
                                        <td>
                                            <div class="d-grid gap-1">
                                                {{-- Tombol Edit --}}
                                                <a href="{{ route('admin.fasilitas.edit', $item->id_fasilitas) }}"
                                                    class="btn btn-sm btn-warning w-100" title="Edit Fasilitas">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>

                                                {{-- Tombol Hapus --}}
                                                <form action="{{ route('admin.fasilitas.destroy', $item->id_fasilitas) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger w-100"
                                                        title="Hapus Fasilitas">
                                                        <i class="fas fa-trash-alt"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
