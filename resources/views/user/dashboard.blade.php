{{-- resources/views/user/dashboard.blade.php --}}

@extends('layouts.user.app')

@section('title', 'Dashboard Pengguna')

@push('styles')
    <link href="{{ asset('css/dashboardpengguna.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container grow">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show text-center mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- HERO SECTION --}}
        <div class="hero-section mb-5">
            <div class="hero-content">

                <p class="welcome-text">Welcome to Roomify</p>

                <h1 class="hero-title">Kelola Pemesanan Anda</h1>

                <p class="hero-subtitle">
                    Temukan pilihan kamar terbaik dengan fasilitas lengkap dan kenyamanan maksimal.
                </p>

                {{-- BUTTON PESAN KAMAR --}}
                @guest
                    <a href="{{ route('login') }}" class="hero-button">Pesan Kamar</a>
                @endguest

            </div>
        </div>
        <div class="stats-card-container mb-5">
            <div class="row row-cols-2 row-cols-md-4 g-2 g-md-0 justify-content-center">

                {{-- Total Kamar (Mengambang di kiri) --}}
                <div class="col stat-item">
                    <span class="stat-value">
                        {{ $totalKamar ?? 0 }}
                    </span>
                    <div class="stat-info-group">
                        <span class="stat-label">Total Kamar</span>
                    </div>
                </div>

                {{-- Kamar Terisi --}}
                <div class="col stat-item">
                    <span class="stat-value">
                        {{ $terisi ?? 0 }}
                    </span>
                    <div class="stat-info-group">
                        <span class="stat-label">Terisi</span>
                    </div>
                </div>

                {{-- Kamar Tersedia --}}
                <div class="col stat-item">
                    <span class="stat-value">
                        {{ $tersedia ?? 0 }}
                    </span>
                    <div class="stat-info-group">
                        <span class="stat-label">Tersedia</span>
                    </div>
                </div>

                {{-- Total Tipe Kamar (Mengambang di kanan) --}}
                <div class="col stat-item">
                    <span class="stat-value">
                        {{ $totalTipe ?? 0 }}
                    </span>
                    <div class="stat-info-group">
                        <span class="stat-label">Total Tipe Kamar</span>
                    </div>
                </div>
            </div>


        </div>
        <div class="service-section container">
            <p class="service-title">POWER OF OUR SERVICE</p>
            <h2 class="display-6 fw-bold mb-5">Tipe Kamar Unggulan Kami</h2>

            <div class="row g-4 justify-content-center">

                {{-- Standard Room --}}
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card service-card shadow-sm">
                        <div class="service-icon-wrapper">
                            <span>🛏️</span>
                        </div>
                        <h3>Standard Room</h3>
                        <p>Kamar dasar yang nyaman dan fungsional untuk menginap solo atau berdua dengan harga terjangkau.
                        </p>
                    </div>
                </div>

                {{-- Deluxe Room --}}
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card service-card shadow-sm">
                        <div class="service-icon-wrapper">
                            <span>⭐</span>
                        </div>
                        <h3>Deluxe Room</h3>
                        <p>Ruangan lebih luas dengan fasilitas premium, ideal untuk meningkatkan kenyamanan Anda.</p>
                    </div>
                </div>

                {{-- Suite Room --}}
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card service-card shadow-sm">
                        <div class="service-icon-wrapper">
                            <span>🏠</span>
                        </div>
                        <h3>Suite Room</h3>
                        <p>Kamar mewah dengan area tamu terpisah, memberikan pengalaman menginap yang eksklusif.</p>
                    </div>
                </div>

                {{--  Family Room --}}
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card service-card shadow-sm">
                        <div class="service-icon-wrapper">
                            <span>👨‍👩‍👧‍👦</span>
                        </div>
                        <h3>Family Room</h3>
                        <p>Dirancang khusus untuk keluarga, menyediakan ruang yang cukup dan ranjang tambahan untuk
                            kenyamanan bersama.</p>
                    </div>
                </div>

            </div>
        </div>
        {{-- FILTER WRAPPER --}}
        <div class="filter-wrapper bg-white p-3 p-md-4 rounded-4 shadow-sm mb-4">

            <form action="{{ route('dashboard') }}" method="GET" class="row g-3 align-items-end">

                {{-- Tipe kamar --}}
                <div class="col-md-4 ">
                    <label class="form-label fw-semibold text-secondary">Tipe Kamar</label>
                    <select name="tipe_kamar" class="form-select form-box">
                        <option value="">Semua Tipe</option>
                        @foreach ($tipeKamarList as $tipe)
                            <option value="{{ $tipe->id_tipe_kamar }}"
                                {{ request('tipe_kamar') == $tipe->id_tipe_kamar ? 'selected' : '' }}>
                                {{ $tipe->nama_tipe_kamar }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Range Harga --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-secondary">Range Harga (Rp)</label>

                    <div class="d-flex gap-2 form-box">
                        <input type="number" class="form-control" placeholder=Minimal name="harga_min"
                            value="{{ request('harga_min') }}">

                        <input type="number" class="form-control" placeholder="Maksimal" name="harga_max"
                            value="{{ request('harga_max') }}">
                    </div>
                </div>

                {{-- Fasilitas --}}
                <div class="col-md-4 position-relative">
                    <label class="form-label fw-semibold text-secondary">Fasilitas</label>

                    <div class="dropdown w-100 position-relative">
                        <button
                            class="btn btn-light border w-100 text-start d-flex justify-content-between align-items-center form-box"
                            type="button" data-bs-toggle="dropdown">
                            Pilih Fasilitas
                            <i class="bi bi-chevron-down"></i>
                        </button>

                        <ul class="dropdown-menu p-3 w-100" style="max-height: 250px; overflow-y:auto;">
                            @foreach ($fasilitasList as $fas)
                                <li class="mb-2">
                                    <label class="d-flex align-items-center gap-2">
                                        <input type="checkbox" class="cek-fasilitas" name="fasilitas[]"
                                            data-nama="{{ $fas->nama_fasilitas }}" value="{{ $fas->id_fasilitas }}"
                                            {{ is_array(request('fasilitas')) && in_array($fas->id_fasilitas, request('fasilitas')) ? 'checked' : '' }}>
                                        {{ $fas->nama_fasilitas }}
                                    </label>
                                </li>
                            @endforeach
                        </ul>

                        {{-- BADGE DIPOSISIKAN ABSOLUTE, TIDAK MENDORONG LAYOUT --}}
                        <div id="selectedFasilitas" class="selected-fasilitas-container">
                        </div>
                    </div>
                </div>



                {{-- BUTTON --}}
                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary px-4">Terapkan</button>
                    <a href="#" id="btnResetJs" class="btn btn-light border px-4">Reset</a>
                </div>

            </form>
        </div>

        @push('scripts')
            <script src="{{ asset('js/dashboard-user.js') }}"></script>
        @endpush

        <h2 class="section-title text-center mb-4 mb-md-5 fw-bold text-primary">
            Pilihan Kamar Tersedia
        </h2>

        @if ($kamarsTersedia->isEmpty())
            <div class="alert alert-warning text-center py-5 rounded-4 shadow-sm border-0">
                <p class="fs-5 mb-1">Maaf, saat ini tidak ada kamar tersedia.</p>
                <p class="text-muted">Silakan coba lagi nanti.</p>
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 g-md-4">
                @foreach ($kamarsTersedia as $kamar)
                    <div class="col">
                        <div class="card room-card-modern h-100">

                            <div class="room-image-wrapper">
                                <img src="{{ asset($kamar->tipeKamar->foto_url) }}" class="room-image-modern"
                                    alt="Kamar {{ $kamar->nomor_kamar }}">
                            </div>

                            <div class="card-body d-flex flex-column">

                                <span class="badge bg-primary px-3 py-2 rounded-pill mb-3 fs-6 fw-semibold badge-short">
                                    {{ $kamar->tipeKamar->nama_tipe_kamar }}
                                </span>

                                <h3 class="card-title fs-4 fw-bold text-dark mb-2">
                                    Kamar No: {{ $kamar->nomor_kamar }}
                                </h3>

                                <p class="text-muted mb-3" style="font-size: 0.95rem;">
                                    {{ Str::limit($kamar->tipeKamar->deskripsi, 80) }}
                                </p>

                                @if ($kamar->tipeKamar->fasilitas->isNotEmpty())
                                    <div class="fasilitas-box mb-3">
                                        <strong class="text-secondary d-block mb-2 small">Fasilitas</strong>

                                        <ul class="list-unstyled row g-2">
                                            @foreach ($kamar->tipeKamar->fasilitas as $fasilitas)
                                                <li class="col-6 d-flex align-items-center gap-1 text-dark small">
                                                    <span class="fasilitas-icon-modern">
                                                        @if ($fasilitas->nama_fasilitas == 'Wifi')
                                                            🌐
                                                        @elseif($fasilitas->nama_fasilitas == 'AC')
                                                            ❄️
                                                        @elseif($fasilitas->nama_fasilitas == 'TV')
                                                            📺
                                                        @else
                                                            ✨
                                                        @endif
                                                    </span>
                                                    {{ $fasilitas->nama_fasilitas }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="mt-auto text-center text-md-end">
                                    <small class="text-muted">Harga/Malam:</small>
                                    <div class="text-success fw-bold fs-4">
                                        Rp {{ number_format($kamar->tipeKamar->harga_per_malam, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="mt-3">
                                    @auth
                                        @if (Auth::user()->hasVerifiedEmail())
                                            <a href="{{ route('booking.create', ['kamar' => $kamar->id_kamar]) }}"
                                                class="btn btn-success w-100 py-2 shadow-sm">
                                                Pesan Sekarang
                                            </a>
                                        @else
                                            <button class="btn btn-secondary w-100 py-2 mb-2" disabled>Verifikasi
                                                Email</button>
                                            <a href="{{ route('verification.notice') }}"
                                                class="btn btn-outline-warning w-100 btn-sm">
                                                Kirim Ulang
                                            </a>
                                        @endif
                                    @endauth

                                    @guest
                                        <a href="{{ route('login') }}" class="btn btn-primary w-100 py-2 shadow-sm">
                                            Login Pesan
                                        </a>
                                    @endguest
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    </div>
@endsection
