@extends('layouts.admin.app')

@section('title', 'Manajemen Pengguna - Roomify Admin')

@section('content')
    <div class="container-fluid px-4">
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

        {{-- Header Halaman --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Manajemen Pengguna</h2>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i> Tambah Pengguna Baru
            </a>
        </div>

        {{-- Tabel Daftar Pengguna --}}
        <div class="card p-4 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Peran</th>
                                <th>Tanggal Bergabung</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if (optional($user->role)->nama_role == 'admin')
                                            <span class="badge bg-danger">
                                                {{ $user->role->nama_role }}
                                            </span>
                                        @else
                                            <span class="badge bg-info text-dark">
                                                {{ $user->role->nama_role ?? 'User' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d M Y') : '-' }}
                                    </td>
                                    <td>
                                        <div class="d-grid gap-1">
                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                                class="btn btn-sm btn-warning w-100" title="Edit Pengguna">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>

                                            {{-- Tombol Hapus --}}
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger w-100"
                                                    title="Hapus Pengguna">
                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada pengguna yang ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
