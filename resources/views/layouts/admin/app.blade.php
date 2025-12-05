<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Admin Dashboard')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('img/Logo_B.png') }}" type="image/png">
    <link href="{{ asset('css/admindashboard.css') }}?v=3" rel="stylesheet">

</head>

<body>

    <x-admin.navbar />

    <div class="container-fluid grow d-flex">
        <x-admin.sidebar />

        {{-- Main Content --}}
        <main class="main-content">
            @yield('content')

            <x-admin.footer />
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admindashboard.js') }}"></script>
    @stack('scripts')
</body>

</html>
