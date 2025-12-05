@extends('layouts.user.app')

@section('title', 'Detail Pesanan #' . $pemesanan->id_pemesanan . ' - Roomify')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                {{-- Tombol Kembali --}}
                <a href="{{ route('profile') }}" class="btn btn-link text-decoration-none mb-3 ps-0">
                    <i class="fas fa-arrow-left me-2"></i> Kembali ke Profil
                </a>

                <div class="card border-0 shadow-sm overflow-hidden">
                    {{-- Header Status --}}
                    <div class="card-header bg-white p-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold text-primary">ID Pesanan: #{{ $pemesanan->id_pemesanan }}</h5>
                                <small class="text-muted">Dibuat pada:
                                    {{ \Carbon\Carbon::parse($pemesanan->created_at)->format('d F Y, H:i') }}</small>
                            </div>
                            <div>
                                @php
                                    $statusClass = match ($pemesanan->status_pemesanan) {
                                        'pending' => 'bg-warning text-dark',
                                        'confirmed' => 'bg-success',
                                        'checked_in' => 'bg-primary',
                                        'checked_out' => 'bg-info',
                                        'paid' => 'bg-dark',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} fs-6 px-3 py-2 rounded-pill">
                                    {{ ucfirst(str_replace('_', ' ', $pemesanan->status_pemesanan)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        {{-- Info Kamar --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3">Detail Kamar</h6>
                                <h4 class="fw-bold mb-1">
                                    {{ $pemesanan->kamar->tipeKamar->nama_tipe_kamar ?? 'Tipe Kamar Tidak Ditemukan' }}</h4>
                                <p class="text-primary fw-bold mb-2">Nomor Kamar: {{ $pemesanan->kamar->nomor_kamar }}</p>
                                @if ($pemesanan->kamar->tipeKamar->fasilitas && $pemesanan->kamar->tipeKamar->fasilitas->isNotEmpty())
                                    <div class="mt-2">
                                        <small class="text-muted d-block mb-1">Fasilitas Kamar:</small>
                                        @foreach ($pemesanan->kamar->tipeKamar->fasilitas as $fasilitas)
                                            <span
                                                class="badge bg-light text-secondary border me-1 mb-1">{{ $fasilitas->nama_fasilitas }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 text-md-end mt-4 mt-md-0">
                                <h6 class="text-muted text-uppercase small fw-bold mb-3">Detail Tamu</h6>
                                <p class="mb-1 fw-bold">{{ $pemesanan->user->name }}</p>
                                <p class="mb-1">{{ $pemesanan->user->email }}</p>
                                <p class="mb-0 text-muted">{{ $pemesanan->jumlah_tamu }} Tamu</p>
                            </div>
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        {{-- Info Waktu --}}
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-4">
                                <small class="text-muted d-block">Check-in</small>
                                <span
                                    class="fw-bold fs-5">{{ \Carbon\Carbon::parse($pemesanan->check_in_date)->format('d M Y') }}</span>
                            </div>
                            <div class="col-6 col-md-4">
                                <small class="text-muted d-block">Check-out</small>
                                <span
                                    class="fw-bold fs-5">{{ \Carbon\Carbon::parse($pemesanan->check_out_date)->format('d M Y') }}</span>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="bg-light p-3 rounded text-center border">
                                    <small class="text-muted d-block">Durasi Menginap</small>
                                    <span class="fw-bold text-primary">
                                        {{ \Carbon\Carbon::parse($pemesanan->check_in_date)->diffInDays(\Carbon\Carbon::parse($pemesanan->check_out_date)) }}
                                        Malam
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Fasilitas Tambahan (jika ada) --}}
                        @if ($pemesanan->fasilitas && $pemesanan->fasilitas->isNotEmpty())
                            <div class="bg-light p-3 rounded border mb-4">
                                <h6 class="fw-bold mb-3">Fasilitas Tambahan</h6>
                                @foreach ($pemesanan->fasilitas as $fasilitas)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{ $fasilitas->nama_fasilitas }}</span>
                                        <span>Rp {{ number_format($fasilitas->biaya_tambahan, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Rincian Harga --}}
                        <div class="bg-light p-4 rounded border">
                            <h6 class="fw-bold mb-3">Rincian Pembayaran</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Harga per Malam</span>
                                <span>Rp
                                    {{ number_format($pemesanan->kamar->tipeKamar->harga_per_malam, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                                <span>Total Durasi</span>
                                <span>x
                                    {{ \Carbon\Carbon::parse($pemesanan->check_in_date)->diffInDays(\Carbon\Carbon::parse($pemesanan->check_out_date)) }}
                                    Malam</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-5">Total Bayar</span>
                                <span class="fw-bold fs-4 text-success">Rp
                                    {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Aksi --}}
                    <div class="card-footer bg-white p-4 border-top">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            @if ($pemesanan->status_pemesanan == 'pending')
                                <a href="{{ route('booking.payment', $pemesanan->id_pemesanan) }}"
                                    class="btn btn-primary fw-bold px-4 py-2">
                                    <i class="fas fa-wallet me-2"></i> Lanjutkan Pembayaran
                                </a>
                            @elseif(in_array($pemesanan->status_pemesanan, ['confirmed', 'checked_in', 'paid']))
                                <button class="btn btn-outline-secondary px-4 py-2" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i> Cetak Bukti
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
