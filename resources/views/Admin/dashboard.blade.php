@extends('layouts.admin.app')

@section('title', 'Dashboard Admin - Roomify')

@section('content')
    <div class="container-fluid px-0">
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

        {{-- Kartu Selamat Datang --}}
        <div class="card p-4 mb-4 card-welcome w-100">
            <div class="card-body">
                <h2 class="card-title mb-3">Selamat Datang, Admin {{ Auth::user()->name }}!</h2>
                <p class="card-text fs-5">Panel Kontrol Administrasi Hotel Roomify.</p>
            </div>
        </div>

        {{-- Bagian Statistik Admin --}}
        <div class="row g-4 mb-5 mx-0">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-admin-stats h-100">
                    <div class="card-body">
                        <h3>Total Kamar</h3>
                        <p>{{ $totalKamar ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-admin-stats h-100">
                    <div class="card-body">
                        <h3>Total Pemesanan</h3>
                        <p>{{ $totalPemesanan ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-admin-stats h-100">
                    <div class="card-body">
                        <h3>Total Pengguna</h3>
                        <p>{{ $totalPengguna ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bagian Pelanggan Sedang Menginap --}}
        <div class="card p-4 shadow-sm w-100">
            <div class="card-header bg-primary text-white text-center fs-5 fw-bold">
                Pelanggan Sedang Menginap
            </div>
            <div class="card-body">
                {{-- Table Responsive Wrapper selalu dirender --}}
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Pelanggan</th>
                                <th>Kamar</th>
                                <th>Tipe Kamar</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Fasilitas Tambahan</th>
                                <th>Total Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pelangganCheckin as $pemesanan)
                                <tr>
                                    <td>{{ $pemesanan->user->name }}</td>
                                    <td>{{ $pemesanan->kamar->nomor_kamar }}</td>
                                    <td>{{ $pemesanan->kamar->tipeKamar->nama_tipe_kamar }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pemesanan->check_in_date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pemesanan->check_out_date)->format('d M Y') }}</td>
                                    <td>
                                        @if ($pemesanan->fasilitas->isNotEmpty())
                                            <ul class="mb-0 ps-3">
                                                @foreach ($pemesanan->fasilitas as $fasilitas)
                                                    <li>{{ $fasilitas->nama_fasilitas }} (Rp
                                                        {{ number_format($fasilitas->biaya_tambahan, 2, ',', '.') }})</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">Tidak Ada</span>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($pemesanan->total_harga, 2, ',', '.') }}</td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            {{-- Tombol Tambah Fasilitas --}}
                                            <a href="{{ route('admin.pemesanans.edit', $pemesanan->id_pemesanan) }}"
                                                class="btn btn-sm btn-info text-white" title="Edit / Tambah Fasilitas">
                                                <i class="fas fa-plus"></i> Fasilitas
                                            </a>
                                            {{-- Tombol Checkout --}}
                                            <form
                                                action="{{ route('admin.pemesanans.checkout', $pemesanan->id_pemesanan) }}"
                                                method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin melakukan checkout untuk pelanggan ini?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success"
                                                    title="Checkout Pelanggan">
                                                    <i class="fas fa-check"></i> Checkout
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i> Tidak ada pelanggan yang sedang menginap
                                            saat ini.
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
