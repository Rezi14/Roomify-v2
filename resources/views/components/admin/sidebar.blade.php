<div class="container-fluid grow d-flex">
    {{-- Sidebar Navigasi Admin --}}
    <div class="sidebar d-flex flex-column">
        <h5 class="text-white mb-4">Navigasi Admin</h5>
        <ul class="nav flex-column mb-auto">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.kamars.*') ? 'active' : '' }}"
                    href="{{ route('admin.kamars.index') }}">
                    <i class="fas fa-bed me-2"></i> Manajemen Kamar
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.tipe_kamars.*') ? 'active' : '' }}"
                    href="{{ route('admin.tipe_kamars.index') }}">
                    <i class="fas fa-hotel me-2"></i> Manajemen Tipe Kamar
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.pemesanans.*') ? 'active' : '' }}"
                    href="{{ route('admin.pemesanans.index') }}">
                    <i class="fas fa-receipt me-2"></i> Manajemen Pemesanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users me-2"></i> Manajemen Pengguna
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.fasilitas.*') ? 'active' : '' }}"
                    href="{{ route('admin.fasilitas.index') }}">
                    <i class="fas fa-spa me-2"></i> Manajemen Fasilitas
                </a>
            </li>
            {{-- Update Link ke Riwayat Pemesanan --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.riwayat.pemesanan') ? 'active' : '' }}"
                    href="{{ route('admin.riwayat.pemesanan') }}">
                    <i class="fas fa-history me-2"></i> Riwayat Pemesanan
                </a>
            </li>
        </ul>

        {{-- Tombol Logout Ditambahkan di Sini --}}
        <div class="mt-4 pt-3 border-top border-secondary">
            @if (Auth::check())
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
