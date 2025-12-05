@extends('layouts.admin.app')

@section('title', 'Tambah Pemesanan - Roomify Admin')

@push('scripts')
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
            <h2 class="mb-0">Tambah Pemesanan Baru</h2>
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

                {{-- Form Tambah Pemesanan --}}
                <form action="{{ route('admin.pemesanans.store') }}" method="POST" id="pemesananForm">
                    @csrf

                    {{-- Pilihan Tipe Pelanggan --}}
                    <div class="form-group mb-3">
                        <label class="fw-bold mb-2">Pilih Pelanggan atau Buat Baru</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="customer_type" id="existingCustomer"
                                value="existing" checked>
                            <label class="form-check-label" for="existingCustomer">Pilih Pelanggan Existing</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="customer_type" id="newCustomer"
                                value="new">
                            <label class="form-check-label" for="newCustomer">Buat Pelanggan Baru</label>
                        </div>
                    </div>

                    {{-- Section Pelanggan Existing --}}
                    <div id="existingCustomerSection" class="mb-3">
                        <div class="form-group">
                            <label for="user_id" class="form-label">Pilih Pelanggan</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">Pilih Pengguna</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Section Pelanggan Baru --}}
                    <div id="newCustomerSection" class="mb-3" style="display: none;">
                        <div class="form-group mb-3">
                            <label for="new_user_name" class="form-label">Nama Pelanggan Baru</label>
                            <input type="text" name="new_user_name" id="new_user_name" class="form-control"
                                value="{{ old('new_user_name') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="new_user_email" class="form-label">Email Pelanggan Baru</label>
                            <input type="email" name="new_user_email" id="new_user_email" class="form-control"
                                value="{{ old('new_user_email') }}">
                        </div>
                    </div>

                    {{-- Pilihan Kamar --}}
                    <div class="form-group mb-3">
                        <label for="kamar_id" class="form-label">Kamar</label>
                        <select name="kamar_id" id="kamar_id" class="form-select" required>
                            <option value="">Pilih Kamar</option>
                            @foreach ($kamars as $kamar)
                                <option value="{{ $kamar->id_kamar }}"
                                    data-harga-per-malam="{{ $kamar->tipeKamar->harga_per_malam }}"
                                    {{ old('kamar_id') == $kamar->id_kamar ? 'selected' : '' }}>
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
                                value="{{ old('check_in_date') }}" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="check_out_date" class="form-label">Tanggal Check-out</label>
                            <input type="date" name="check_out_date" id="check_out_date" class="form-control"
                                value="{{ old('check_out_date') }}" required>
                        </div>
                    </div>

                    {{-- Jumlah Tamu & Total Harga --}}
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="jumlah_tamu" class="form-label">Jumlah Tamu</label>
                            <input type="number" name="jumlah_tamu" id="jumlah_tamu" class="form-control"
                                value="{{ old('jumlah_tamu') }}" required min="1">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="total_harga" class="form-label">Total Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="total_harga" id="total_harga"
                                    class="form-control bg-light fw-bold" step="0.01"
                                    value="{{ old('total_harga') ?? 0 }}" required readonly>
                            </div>
                        </div>
                    </div>

                    {{-- Status Pemesanan --}}
                    <div class="form-group mb-3">
                        <label for="status_pemesanan" class="form-label">Status Pemesanan</label>
                        <select name="status_pemesanan" id="status_pemesanan" class="form-select" required>
                            <option value="pending" {{ old('status_pemesanan') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="confirmed" {{ old('status_pemesanan') == 'confirmed' ? 'selected' : '' }}>
                                Confirmed</option>
                            <option value="checked_in" {{ old('status_pemesanan') == 'checked_in' ? 'selected' : '' }}>
                                Checked In</option>
                            <option value="checked_out" {{ old('status_pemesanan') == 'checked_out' ? 'selected' : '' }}>
                                Checked Out</option>
                            <option value="cancelled" {{ old('status_pemesanan') == 'cancelled' ? 'selected' : '' }}>
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
                                                <input class="form-check-input" type="checkbox"
                                                    name="fasilitas_tambahan[]" id="fasilitas_{{ $item->id_fasilitas }}"
                                                    value="{{ $item->id_fasilitas }}"
                                                    data-biaya-tambahan="{{ $item->biaya_tambahan }}"
                                                    {{ in_array($item->id_fasilitas, old('fasilitas_tambahan', [])) ? 'checked' : '' }}
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
                            @if ($fasilitas->isEmpty())
                                <div class="col-12">
                                    <div class="alert alert-secondary text-center mb-0">
                                        Tidak ada fasilitas tambahan yang tersedia saat ini.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Pemesanan
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
                // Elemen-elemen DOM
                const kamarSelect = document.getElementById('kamar_id');
                const checkInDateInput = document.getElementById('check_in_date');
                const checkOutDateInput = document.getElementById('check_out_date');
                const fasilitasCheckboxes = document.querySelectorAll('input[name="fasilitas_tambahan[]"]');
                const totalHargaInput = document.getElementById('total_harga');

                const customerTypeRadios = document.querySelectorAll('input[name="customer_type"]');
                const existingCustomerSection = document.getElementById('existingCustomerSection');
                const newCustomerSection = document.getElementById('newCustomerSection');
                const userIdSelect = document.getElementById('user_id');
                const newUserNameInput = document.getElementById('new_user_name');
                const newUserEmailInput = document.getElementById('new_user_email');

                // Fungsi Toggle Field Pelanggan (Existing / Baru)
                function toggleCustomerFields() {
                    if (document.getElementById('existingCustomer').checked) {
                        existingCustomerSection.style.display = 'block';
                        newCustomerSection.style.display = 'none';
                        userIdSelect.setAttribute('required', 'required');
                        newUserNameInput.removeAttribute('required');
                        newUserEmailInput.removeAttribute('required');
                    } else {
                        existingCustomerSection.style.display = 'none';
                        newCustomerSection.style.display = 'block';
                        userIdSelect.removeAttribute('required');
                        newUserNameInput.setAttribute('required', 'required');
                        newUserEmailInput.setAttribute('required', 'required');
                    }

                    // Reset nilai input saat toggle (opsional)
                    if (document.getElementById('existingCustomer').checked) {
                        newUserNameInput.value = '';
                        newUserEmailInput.value = '';
                    } else {
                        userIdSelect.value = '';
                    }
                }

                // Event Listener untuk Radio Button Pelanggan
                customerTypeRadios.forEach(radio => {
                    radio.addEventListener('change', toggleCustomerFields);
                });

                // Fungsi Kalkulasi Total Harga Otomatis
                function calculateTotalPrice() {
                    let totalHarga = 0;

                    // Harga Kamar
                    const selectedKamarOption = kamarSelect.options[kamarSelect.selectedIndex];
                    const hargaPerMalam = parseFloat(selectedKamarOption.dataset.hargaPerMalam || 0);

                    // Durasi Menginap
                    const checkInDate = new Date(checkInDateInput.value);
                    const checkOutDate = new Date(checkOutDateInput.value);

                    if (checkInDate && checkOutDate && checkOutDate > checkInDate) {
                        const diffTime = Math.abs(checkOutDate - checkInDate);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        totalHarga += hargaPerMalam * diffDays;
                    }

                    // Biaya Fasilitas Tambahan
                    fasilitasCheckboxes.forEach(checkbox => {
                        if (checkbox.checked) {
                            totalHarga += parseFloat(checkbox.dataset.biayaTambahan || 0);
                        }
                    });

                    // Update Input Total Harga
                    totalHargaInput.value = totalHarga.toFixed(2);
                }

                // Event Listener untuk Kalkulasi Harga
                kamarSelect.addEventListener('change', calculateTotalPrice);
                checkInDateInput.addEventListener('change', calculateTotalPrice);
                checkOutDateInput.addEventListener('change', calculateTotalPrice);
                fasilitasCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', calculateTotalPrice);
                });

                // Inisialisasi saat halaman dimuat
                toggleCustomerFields();
                calculateTotalPrice();
            });
        </script>
    @endpush
@endsection
