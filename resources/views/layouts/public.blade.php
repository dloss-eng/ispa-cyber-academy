<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

    <!-- Navbar simple -->
    <header class="topbar">
        <a href="/">ISPA CYBER ACADEMY</a>

        <nav>
            <a href="/">Accueil</a>
            <a href="/courses">Cours</a>
            <a href="/about">À propos</a>
            <a href="/contact">Contact</a>
        </nav>

        <a href="{{ route('login') }}" class="btn">Se connecter</a>
    </header>

    <main>
        @yield('content')
    </main>

</body>
</html>