<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Petstore')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<header>
    <h1>Petstore Client</h1>
</header>

<main>
    @yield('content')
</main>

<footer>
    <small>Laravel demo</small>
</footer>

<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
