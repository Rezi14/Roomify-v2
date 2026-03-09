@extends('layouts.admin.app')

@section('title', 'Detail Riwayat Pemesanan - Roomify Admin')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Detail Riwayat Pemesanan #{{ $pemesanan->id_pemesanan }}</h1>
            <a href="{{ route('admin.riwayat.pemesanan') }}" class="btn btn-danger text-white btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informasi Pesanan</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 30%">Nama Tamu</th>
                                <td>: {{ $pemesanan->user->name ?? 'User Terhapus' }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>: {{ $pemesanan->user->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nomor Kamar</th>
                                <td>: {{ $pemesanan->kamar->nomor_kamar ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tipe Kamar</th>
                                <td>: {{ $pemesanan->kamar->tipeKamar->nama_tipe_kamar ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Check-In</th>
                                <td>: {{ $pemesanan->check_in_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Check-Out</th>
                                <td>: {{ $pemesanan->check_out_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Jumlah Tamu</th>
                                <td>: {{ $pemesanan->jumlah_tamu }} Orang</td>
                            </tr>
                        </table>

                        <hr>
                        <h6 class="font-weight-bold">Fasilitas Tambahan:</h6>
                        @if ($pemesanan->fasilitas->count() > 0)
                            <ul>
                                @foreach ($pemesanan->fasilitas as $fasilitas)
                                    <li>{{ $fasilitas->nama_fasilitas }} (+ Rp
                                        {{ number_format($fasilitas->biaya_tambahan, 0, ',', '.') }})</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted small">Tidak ada fasilitas tambahan.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Detail Transaksi</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="small font-weight-bold text-secondary">Status Pemesanan</label>
                            <div>
                                @if ($pemesanan->status_pemesanan == \App\Enums\StatusPemesanan::PAID)
                                    <span class="badge badge-success px-3 py-2" style="font-size: 1rem;">LUNAS (Paid)</span>
                                @elseif($pemesanan->status_pemesanan == \App\Enums\StatusPemesanan::CANCELLED)
                                    <span class="badge badge-danger px-3 py-2" style="font-size: 1rem;">DIBATALKAN</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="small font-weight-bold text-secondary">Total Biaya</label>
                            <h4 class="font-weight-bold text-success">
                                Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                            </h4>
                        </div>

                        <div class="mb-3">
                            <label class="small font-weight-bold text-secondary">Waktu Transaksi</label>
                            <p>{{ $pemesanan->updated_at->format('d M Y, H:i:s') }}</p>
                        </div>

                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle"></i> Data ini adalah arsip riwayat dan tidak dapat diedit kembali.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
