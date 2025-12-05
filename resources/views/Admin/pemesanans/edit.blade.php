@extends('layouts.admin.app')

@section('title', 'Edit Pemesanan -Roomify Admin')

@push('style')
    <style>
        .hover-shadow:hover {
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            transform: translateY(-2px);
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        {{-- Header Halaman --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Edit Pemesanan #{{ $pemesanan->id_pemesanan }}</h2>
            <a href="{{ route('admin.pemesanans.index') }}" class="btn bg-danger text-white">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Pemesanan
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
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- Form Edit Pemesanan --}}
                <form action="{{ route('admin.pemesanans.update', $pemesanan->id_pemesanan) }}" method="POST"
                    id="pemesananEditForm">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-3">
                        <label for="user_id" class="form-label">Pengguna</label>
                        <select name="user_id" id="user_id" class="form-select bg-light" required disabled>
                            <option value="">Pilih Pengguna</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('user_id', $pemesanan->user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="user_id" value="{{ $pemesanan->user_id }}">
                    </div>

                    {{-- Pilihan Kamar --}}
                    <div class="form-group mb-3">
                        <label for="kamar_id" class="form-label">Kamar</label>
                        <select name="kamar_id" id="kamar_id" class="form-select" required>
                            <option value="">Pilih Kamar</option>
                            @foreach ($kamars as $kamar)
                                <option value="{{ $kamar->id_kamar }}"
                                    data-harga-per-malam="{{ $kamar->tipeKamar->harga_per_malam }}"
                                    {{ old('kamar_id', $pemesanan->kamar_id) == $kamar->id_kamar ? 'selected' : '' }}>
                                    Kamar No. {{ $kamar->nomor_kamar }} (Tipe:
                                    {{ $kamar->tipeKamar->nama_tipe_kamar ?? 'N/A' }}) - Rp
                                    {{ number_format($kamar->tipeKamar->harga_per_malam ?? 0, 0, ',', '.') }} / malam
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tanggal Check-in & Check-out --}}
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="check_in_date" class="form-label">Tanggal Check-in</label>
                            <input type="date" name="check_in_date" id="check_in_date" class="form-control"
                                value="{{ old('check_in_date', \Carbon\Carbon::parse($pemesanan->check_in_date)->format('Y-m-d')) }}"
                                required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="check_out_date" class="form-label">Tanggal Check-out</label>
                            <input type="date" name="check_out_date" id="check_out_date" class="form-control"
                                value="{{ old('check_out_date', \Carbon\Carbon::parse($pemesanan->check_out_date)->format('Y-m-d')) }}"
                                required>
                        </div>
                    </div>

                    {{-- Jumlah Tamu & Total Harga --}}
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="jumlah_tamu" class="form-label">Jumlah Tamu</label>
                            <input type="number" name="jumlah_tamu" id="jumlah_tamu" class="form-control"
                                value="{{ old('jumlah_tamu', $pemesanan->jumlah_tamu) }}" required min="1">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="total_harga" class="form-label">Total Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="total_harga" id="total_harga"
                                    class="form-control bg-light fw-bold" step="0.01"
                                    value="{{ old('total_harga', $pemesanan->total_harga) }}" required readonly>
                            </div>
                        </div>
                    </div>

                    {{-- Status Pemesanan --}}
                    <div class="form-group mb-4">
                        <label for="status_pemesanan" class="form-label">Status Pemesanan</label>
                        <select name="status_pemesanan" id="status_pemesanan" class="form-select" required>
                            <option value="pending"
                                {{ old('status_pemesanan', $pemesanan->status_pemesanan) == 'pending' ? 'selected' : '' }}>
                                Pending</option>
                            <option value="confirmed"
                                {{ old('status_pemesanan', $pemesanan->status_pemesanan) == 'confirmed' ? 'selected' : '' }}>
                                Confirmed</option>
                            <option value="checked_in"
                                {{ old('status_pemesanan', $pemesanan->status_pemesanan) == 'checked_in' ? 'selected' : '' }}>
                                Checked In</option>
                            <option value="checked_out"
                                {{ old('status_pemesanan', $pemesanan->status_pemesanan) == 'checked_out' ? 'selected' : '' }}>
                                Checked Out</option>
                            <option value="cancelled"
                                {{ old('status_pemesanan', $pemesanan->status_pemesanan) == 'cancelled' ? 'selected' : '' }}>
                                Cancelled</option>
                        </select>
                    </div>

                    {{-- Fasilitas Tambahan --}}
                    <div class="mb-4">
                        <label class="fw-bold mb-3 d-block">Fasilitas Tambahan</label>
                        <div class="row g-3">
                            @foreach ($fasilitas as $item)
                                <div class="col-lg-4 col-md-6">
                                    <div class="card h-100 border shadow-sm position-relative hover-shadow transition-all">
                                        <div class="card-body p-3 d-flex align-items-center">
                                            <div class="form-check w-100 m-0">
                                                <input class="form-check-input" type="checkbox" name="fasilitas_tambahan[]"
                                                    id="fasilitas_{{ $item->id_fasilitas }}"
                                                    value="{{ $item->id_fasilitas }}"
                                                    data-biaya-tambahan="{{ $item->biaya_tambahan }}"
                                                    {{ in_array($item->id_fasilitas, old('fasilitas_tambahan', $selectedFasilitas)) ? 'checked' : '' }}
                                                    style="transform: scale(1.2); margin-top: 0.3rem;">
                                                <label
                                                    class="form-check-label d-flex justify-content-between align-items-center w-100 ps-2 stretched-link cursor-pointer"
                                                    for="fasilitas_{{ $item->id_fasilitas }}" style="cursor: pointer;">
                                                    <span class="fw-medium text-dark">{{ $item->nama_fasilitas }}</span>
                                                    <span
                                                        class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill">
                                                        +Rp {{ number_format($item->biaya_tambahan, 0, ',', '.') }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Pemesanan
                        </button>
                        <a href="{{ route('admin.pemesanans.index') }}" class="btn btn-danger text-white">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const kamarSelect = document.getElementById('kamar_id');
                const checkInDateInput = document.getElementById('check_in_date');
                const checkOutDateInput = document.getElementById('check_out_date');
                const fasilitasCheckboxes = document.querySelectorAll('input[name="fasilitas_tambahan[]"]');
                const totalHargaInput = document.getElementById('total_harga');

                function calculateTotalPrice() {
                    let totalHarga = 0;

                    // Menghitung harga kamar berdasarkan durasi menginap
                    const selectedKamarOption = kamarSelect.options[kamarSelect.selectedIndex];
                    const hargaPerMalam = parseFloat(selectedKamarOption.dataset.hargaPerMalam || 0);

                    const checkInDate = new Date(checkInDateInput.value);
                    const checkOutDate = new Date(checkOutDateInput.value);

                    if (checkInDate && checkOutDate && checkOutDate > checkInDate) {
                        const diffTime = Math.abs(checkOutDate - checkInDate);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        totalHarga += hargaPerMalam * diffDays;
                    }

                    // Menambahkan biaya fasilitas tambahan
                    fasilitasCheckboxes.forEach(checkbox => {
                        if (checkbox.checked) {
                            totalHarga += parseFloat(checkbox.dataset.biayaTambahan || 0);
                        }
                    });

                    totalHargaInput.value = totalHarga.toFixed(2);
                }

                // Menambahkan event listener untuk memanggil perhitungan setiap kali input berubah
                kamarSelect.addEventListener('change', calculateTotalPrice);
                checkInDateInput.addEventListener('change', calculateTotalPrice);
                checkOutDateInput.addEventListener('change', calculateTotalPrice);
                fasilitasCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', calculateTotalPrice);
                });

                // Memanggil perhitungan saat halaman pertama kali dimuat (untuk inisialisasi harga edit)
                calculateTotalPrice();
            });
        </script>
    @endpush
@endsection
