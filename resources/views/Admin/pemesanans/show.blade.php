@extends('layouts.admin.app')

@section('title', 'Detail Pemesanan - Roomify Admin')

@section('content')
    <div class="container-fluid px-4">
        {{-- Header Halaman --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Detail Pemesanan #{{ $pemesanan->id_pemesanan }}</h2>
            <a href="{{ route('admin.pemesanans.index') }}" class="btn bg-danger text-white">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Pemesanan
            </a>
        </div>

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

        {{-- Card Detail --}}
        <div class="card p-4 shadow-sm mb-4">
            <div class="card-body">
                {{-- Informasi Utama --}}
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Pelanggan</div>
                    <div class="col-md-9">: {{ $pemesanan->user->name ?? 'N/A' }} <span
                            class="text-muted">({{ $pemesanan->user->email ?? 'N/A' }})</span></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Kamar</div>
                    <div class="col-md-9">:
                        {{ $pemesanan->kamar->nomor_kamar ?? 'N/A' }}
                        <span class="badge bg-light text-dark border ms-2">Tipe:
                            {{ $pemesanan->kamar->tipeKamar->nama_tipe_kamar ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Tanggal Check-in</div>
                    <div class="col-md-9">: {{ \Carbon\Carbon::parse($pemesanan->check_in_date)->format('d F Y') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Tanggal Check-out</div>
                    <div class="col-md-9">: {{ \Carbon\Carbon::parse($pemesanan->check_out_date)->format('d F Y') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Jumlah Tamu</div>
                    <div class="col-md-9">: {{ $pemesanan->jumlah_tamu }} Orang</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Status Pemesanan</div>
                    <div class="col-md-9">:
                        @php
                            $badgeClass = match ($pemesanan->status_pemesanan) {
                                \App\Enums\StatusPemesanan::PENDING     => 'bg-warning text-dark',
                                \App\Enums\StatusPemesanan::CONFIRMED   => 'bg-success',
                                \App\Enums\StatusPemesanan::CHECKED_IN  => 'bg-primary',
                                \App\Enums\StatusPemesanan::CHECKED_OUT => 'bg-info text-dark',
                                \App\Enums\StatusPemesanan::PAID        => 'bg-dark',
                                \App\Enums\StatusPemesanan::CANCELLED   => 'bg-secondary',
                                default => 'bg-light text-dark border',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                            {{ ucfirst(str_replace('_', ' ', $pemesanan->status_pemesanan->value)) }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Total Harga</div>
                    <div class="col-md-9 text-primary fw-bold">: Rp
                        {{ number_format($pemesanan->total_harga, 2, ',', '.') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Tanggal Dibuat</div>
                    <div class="col-md-9">: {{ \Carbon\Carbon::parse($pemesanan->created_at)->format('d F Y H:i:s') }}
                    </div>
                </div>

                {{-- Fasilitas Tambahan --}}
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Fasilitas Tambahan: </div>
                    <div class="col-md-9">:
                        @if ($pemesanan->fasilitas->isNotEmpty())
                            <ul class="list-group list-group-flush mt-2" style="max-width: 500px;">
                                @foreach ($pemesanan->fasilitas as $fasilitas)
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 border-0">
                                        <span><i
                                                class="fas fa-check-circle text-success me-2"></i>{{ $fasilitas->nama_fasilitas }}</span>
                                        <span class="text-muted small">Rp
                                            {{ number_format($fasilitas->biaya_tambahan, 2, ',', '.') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted fst-italic">Tidak Ada</span>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                {{-- Bagian Aksi Admin --}}
                <h5 class="mb-3"><i class="fas fa-cogs me-2"></i>Aksi Admin</h5>
                <div class="d-flex gap-2 flex-wrap">

                    {{-- Tombol Konfirmasi --}}
                    @if ($pemesanan->status_pemesanan === \App\Enums\StatusPemesanan::PENDING)
                        <form action="{{ route('admin.pemesanans.confirm', $pemesanan->id_pemesanan) }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin mengkonfirmasi pemesanan ini?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle me-1"></i> Terima Pesanan
                            </button>
                        </form>
                    @else
                        <button class="btn btn-secondary" disabled title="Hanya tersedia jika status Pending">
                            <i class="fas fa-check-circle me-1"></i> Terima Pesanan
                        </button>
                    @endif

                    {{-- Tombol Check-in (Hanya jika Confirmed) --}}
                    @if ($pemesanan->status_pemesanan === \App\Enums\StatusPemesanan::CONFIRMED)
                        <form action="{{ route('admin.pemesanans.checkin', $pemesanan->id_pemesanan) }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin melakukan check-in?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-1"></i> Check-in
                            </button>
                        </form>
                    @else
                        <button class="btn btn-secondary" disabled title="Hanya tersedia jika status Confirmed">
                            <i class="fas fa-sign-in-alt me-1"></i> Check-in
                        </button>
                    @endif

                    {{-- Tombol Edit --}}
                    <a href="{{ route('admin.pemesanans.edit', $pemesanan->id_pemesanan) }}"
                        class="btn btn-warning text-dark">
                        <i class="fas fa-edit me-1"></i> Edit Detail
                    </a>

                    {{-- Tombol Hapus --}}
                    <form action="{{ route('admin.pemesanans.destroy', $pemesanan->id_pemesanan) }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemesanan ini? Tindakan ini tidak dapat dibatalkan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-1"></i> Hapus Pemesanan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
