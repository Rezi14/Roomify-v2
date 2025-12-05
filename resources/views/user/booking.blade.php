@extends('layouts.user.app')

@section('title', 'Pemesanan')

@push('styles')
    <link href="{{ asset('css/booking.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container checkout-container">

        <div class="row">

            <div class="col-lg-7">

                <div class="left-box">
                    <a href="{{ route('dashboard') }}" class="back-link">
                        <i class="fas fa-chevron-left me-2" style="margin-left: 20px; font-size:16px;"></i>
                        <span style="font-size:16px;">Kembali</span>
                    </a>

                    <img src="{{ asset($kamar->tipeKamar->foto_url) }}" class="room-photo" alt="Foto kamar">

                    <h3 class="mt-4">Detail Kamar</h3>

                    <p><strong>Nomor Kamar:</strong> {{ $kamar->nomor_kamar }}</p>
                    <p><strong>Tipe Kamar:</strong> {{ $kamar->tipeKamar->nama_tipe_kamar }}</p>
                    <p><strong>Kapasitas Maksimal:</strong> {{ $maxTamu }} Orang</p>
                    <p><strong>Harga Per Malam:</strong> Rp
                        {{ number_format($kamar->tipeKamar->harga_per_malam, 0, ',', '.') }}</p>
                    <p><strong>Deskripsi Tipe:</strong> {{ $kamar->tipeKamar->deskripsi }}</p>
                    <p><strong>Status:</strong> {{ $kamar->status_kamar ? 'Tersedia' : 'Tidak Tersedia' }}</p>

                    @if ($kamar->tipeKamar->fasilitas->isNotEmpty())
                        <h5 class="mt-4">Fasilitas Termasuk (Gratis):</h5>
                        <ul>
                            @foreach ($kamar->tipeKamar->fasilitas as $f)
                                <li>{{ $f->nama_fasilitas }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="col-lg-5">
                <div class="order-card">

                    <h4 class="mb-3 fw-bold">Formulir Pemesanan</h4>
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    {{-- Tampilkan Error jika validasi gagal --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('booking.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kamar_id" value="{{ $kamar->id_kamar }}">

                        <label class="form-label">Tanggal Check-in</label>
                        <input type="date" class="form-control mb-3" name="check_in_date"
                            value="{{ old('check_in_date') }}" required>

                        <label class="form-label">Tanggal Check-out</label>
                        <input type="date" class="form-control mb-3" name="check_out_date"
                            value="{{ old('check_out_date') }}" required>
                        <label class="form-label">
                            Jumlah Tamu <small class="text-danger">(Maks: {{ $maxTamu }} orang)</small>
                        </label>
                        <input type="number" class="form-control mb-3" name="jumlah_tamu"
                            value="{{ old('jumlah_tamu', 1) }}" min="1" max="{{ $maxTamu }}" required>

                        @if ($fasilitasTersedia->isNotEmpty())
                            <h6 class="fw-bold mt-4 mb-2">Fasilitas Tambahan (Opsional)</h6>

                            @foreach ($fasilitasTersedia as $fs)
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <input type="checkbox" name="fasilitas_ids[]" value="{{ $fs->id_fasilitas }}">
                                        {{ $fs->nama_fasilitas }}
                                    </div>
                                    <span class="text-primary fw-bold">
                                        + Rp {{ number_format($fs->biaya_tambahan, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        @endif

                        <hr class="my-4">

                        <div class="total-box d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">TOTAL</h5>
                            <h4 class="fw-bold text-primary mb-0" id="totalHarga">Rp 0</h4>
                        </div>

                        <button type="submit" class="order-btn">Konfirmasi Pemesanan</button>

                        <input type="hidden" id="hargaPerMalam" value="{{ $kamar->tipeKamar->harga_per_malam }}">

                        @foreach ($fasilitasTersedia as $fs)
                            <input type="hidden" class="hargaFasilitas" data-id="{{ $fs->id_fasilitas }}"
                                value="{{ $fs->biaya_tambahan }}">
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('js/booking.js') }}"></script>
    @endpush
@endsection
