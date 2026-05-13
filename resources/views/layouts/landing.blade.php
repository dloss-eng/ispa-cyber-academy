<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>@yield('title', 'ISPA Cyber Academy') — Cybersécurité CI</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('styles')
<script>
// ── Applique immédiatement le thème sauvegardé (anti-flash) ──
(function(){
    const t = localStorage.getItem('ispa-theme') || 'dark';
    document.documentElement.setAttribute('data-theme', t);
})();
</script>
</head>

<body class="landing-body">

    <!-- BLOBS -->
    <div class="blob blob-orange"></div>
    <div class="blob blob-green"></div>

    <!-- NAVBAR -->
    @include('partials.navbar')

    <!-- CONTENT -->
    <main class="landing-main">
        @yield('content')
    </main>

    <!-- FOOTER -->
    @include('partials.footer')

    @stack('scripts')

    <script>
    // ── toggleTheme global (utilisé par navbar et dashboard) ──
    function toggleTheme() {
        const html = document.documentElement;
        const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('ispa-theme', next);
    }
    </script>
</body>
</html>
