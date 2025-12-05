<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Roomify</title>
    <link rel="icon" href="{{ asset('img/Logo_B.png') }}">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body>
    @yield('content')
    @stack('scripts')
</body>

</html>
