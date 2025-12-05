<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-modern fixed-top">
        <div class="container-fluid px-4">
            {{-- Brand --}}
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('img/Logo Roomify_B.png') }}" alt="Logo"
                    style="height: 25px; width: auto; margin-right: 10px;">
            </a>
            {{-- Toggler --}}
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                {{-- Menu kiri --}}
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 order-lg-1">

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold' : '' }}"
                            href="{{ route('dashboard') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active fw-bold' : '' }}"
                            href="{{ route('contact') }}">Contact</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('profile') ? 'active fw-bold' : '' }}"
                                href="{{ route('profile') }}">Profil</a>
                        </li>
                    @endauth
                </ul>
                {{-- Login/Logout --}}
                <div class="d-flex mt-3 mt-lg-0 align-items-center order-lg-3">
                    @if (Auth::check())
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-logout">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-login me-3">Sign In</a>
                        <a href="{{ route('register') }}" class="btn btn-register">Sign Up</a>
                    @endif
                </div>

            </div>
        </div>
    </nav>
</body>
</html>
