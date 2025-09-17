<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/sass/login.scss', 'resources/js/app.js'])

</head>

<body>

    <div class="login-menu">
        <div class="container">
            <nav class="nav">
                <a class="nav-link active" href="#">AD Manager</a>
            </nav>
        </div>
    </div>

    <div class="container h-100">

        @yield('content')

    </div>

</body>

</html>
