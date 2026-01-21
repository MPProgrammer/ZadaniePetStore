<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Petstore')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <div id="site">
        <header>
            <h1>@yield('h1_title', 'Petstore')</h1>
            @yield('header')
        </header>

        <main>
            @yield('content')
        </main>

        <footer>
            <div id="footer-info">
                <p>&copy; {{ date('Y') }} Zadanie Rekrutacyjne Sellasist. Mariusz Panek</p>
            </div>
            @yield('footer')
        </footer>
    </div>

    <script src="{{ asset('js/app.js') }}" defer></script>
</body>

</html>
