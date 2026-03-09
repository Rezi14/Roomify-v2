@extends('layouts.user.app')

@section('title', 'Kontak - Roomify')


@section('content')
    <style>
        body {
            background: #f5f7fb;
        }

        .profile-card {
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            padding: 25px;
        }

        .avatar-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4c8dff, #6fa8ff);
            font-size: 48px;
            font-weight: bold;
        }

        .stat-card {
            background: #fff;
            padding: 18px;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1;
        }

        .stat-label {
            font-size: 16px;
            color: #6c757d;
            font-weight: 500;
        }


        .table-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            padding: 25px;
        }

        table thead tr {
            background: #f1f3f7 !important;
        }

        table th {
            font-size: 14px;
            font-weight: 600;
            color: #444;
        }

        table td {
            padding: 14px 10px;
            font-size: 15px;
        }

        .badge-status {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
        }

        .badge-success {
            background: #e8f8ef;
            color: #2fa568;
        }

        .badge-warning {
            background: #fff8e6;
            color: #c48a1b;
        }

        .badge-danger {
            background: #ffeaea;
            color: #d32f2f;
        }

        .btn-edit {
            border-radius: 10px;
            padding: 6px 16px;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
    <div class="container py-5">

        <div class="row">

            {{-- LEFT PROFILE CARD --}}
            <div class="col-lg-4 mb-4">
                <div class="profile-card text-center">

                    <div class="d-flex justify-content-center mb-3">
                        <div class="avatar-circle d-flex justify-content-center align-items-center text-white shadow">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>

                    <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>

                    <div class="mt-3 mb-4">
                        <span class="badge bg-primary text-white px-3 py-2 rounded-pill">
                            {{ $user->nama_role ?? 'Member' }}
                        </span>
                    </div>

                    <hr>

                    <div class="text-start">
                        <p class="text-muted fw-bold small mb-2">PENGATURAN AKUN</p>

                        <button class="btn btn-light w-100 text-start mb-2" data-bs-toggle="modal"
                            data-bs-target="#editProfileModal">
                            <i class="fas fa-user-edit me-2 text-primary"></i> Edit Profil
                        </button>

                        <button class="btn btn-light w-100 text-start mb-2" data-bs-toggle="modal"
                            data-bs-target="#changePasswordModal">
                            <i class="fas fa-key me-2 text-primary"></i> Ubah Password
                        </button>

                        <form action="{{ route('logout') }}" method="POST" class="mt-3">
                            @csrf
                            <button class="btn btn-outline-danger w-100">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </div>

                </div>
            </div>

            <div class="col-lg-8">

                {{-- TOP STAT CARDS --}}
                <div class="row g-3 mb-4">

                    <div class="col-md-4">
                        <div class="stat-card d-flex align-items-center justify-content-center gap-3">
                            <div class="stat-number">{{ $user->pemesanans->count() }}</div>
                            <div class="stat-label">Total Pesanan</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="stat-card d-flex align-items-center justify-content-center gap-3">
                            <div class="stat-number">
                                {{ $user->pemesanans->where('status_pemesanan', \App\Enums\StatusPemesanan::CONFIRMED)->count() }}
                            </div>
                            <div class="stat-label">Selesai</div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="stat-card d-flex align-items-center justify-content-center gap-3">
                            <div class="stat-number">
                                {{ $user->pemesanans->where('status_pemesanan', \App\Enums\StatusPemesanan::PENDING)->count() }}
                            </div>
                            <div class="stat-label">Menunggu</div>
                        </div>
                    </div>

                </div>

                {{-- TABLE ORDER HISTORY --}}
                <div class="table-card">

                    <h5 class="fw-bold mb-3 text-primary">
                        <i class="fas fa-history me-2"></i>Riwayat Pemesanan
                    </h5>

                    @if ($user->pemesanans && $user->pemesanans->count() > 0)

                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Kamar</th>
                                        <th>Check-in</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th class="text-end pe-3">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($user->pemesanans as $pemesanan)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">
                                                    {{ $pemesanan->kamar->tipeKamar->nama_tipe ?? 'Kamar' }}
                                                </div>

                                                <small class="text-muted">
                                                    No. {{ $pemesanan->kamar->nomor_kamar }}
                                                </small>
                                            </td>

                                            <td>
                                                {{ \Carbon\Carbon::parse($pemesanan->tanggal_check_in)->format('d M Y') }}
                                            </td>

                                            <td class="fw-bold">
                                                Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                                            </td>

                                            <td>
                                                <span
                                                    class="badge-status rounded-pill
                                        @if ($pemesanan->status_pemesanan == \App\Enums\StatusPemesanan::CONFIRMED) badge-success
                                        @elseif ($pemesanan->status_pemesanan == \App\Enums\StatusPemesanan::PENDING)
                                            badge-warning
                                        @elseif ($pemesanan->status_pemesanan == \App\Enums\StatusPemesanan::PAID) badge-success
                                        @else
                                            badge-danger @endif
                                    ">
                                                    {{ ucfirst($pemesanan->status_pemesanan->value) }}
                                                </span>
                                            </td>

                                            <td class="text-end pe-3">

                                                @if ($pemesanan->status_pemesanan == \App\Enums\StatusPemesanan::PENDING)
                                                    <a href="{{ route('booking.payment', $pemesanan->id_pemesanan) }}"
                                                        class="btn btn-primary btn-edit">
                                                        Bayar
                                                    </a>
                                                @else
                                                    <a href="{{ route('booking.detail', $pemesanan->id_pemesanan) }}"
                                                        class="btn btn-outline-primary btn-edit border">
                                                        Detail
                                                    </a>
                                                @endif

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted">Belum ada riwayat pemesanan.</p>
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-edit">
                                Cari Kamar
                            </a>
                        </div>
                    @endif

                </div>
            </div>

        </div>

    </div>

    {{-- MODAL EDIT PROFILE --}}
    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="fw-bold">Edit Profil</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control mb-3" value="{{ $user->name }}"
                            required>

                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- MODAL PASSWORD --}}
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="fw-bold">Ubah Password</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <label class="form-label">Password Lama</label>
                        <input type="password" name="current_password" class="form-control mb-3" required>

                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control mb-3" required minlength="8">

                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary">Ubah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
