<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — ISPA Cyber Academy</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('styles')
<script>
(function(){
    const t = localStorage.getItem('ispa-theme') || 'dark';
    document.documentElement.setAttribute('data-theme', t);
})();
</script>
</head>
<body>
<div style="display:flex;min-height:100vh;position:relative;z-index:1">

{{-- ✅ Sidebar supprimée --}}

<div id="main" style="margin-left:0;width:100%">
    <div class="topbar" style="justify-content:flex-end">
        {{-- Bouton Mode Clair / Sombre uniquement --}}
        <button id="themeToggle" onclick="toggleTheme()" class="theme-btn" title="Basculer le thème">
            <span id="themeIcon">
                <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
                <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
            </span>
        </button>
    </div>

    <div class="vue" style="padding:0">
        @if(session('success'))
            <div class="alert-success">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert-error">
                @foreach($errors->all() as $e)
                    <div style="display:flex;align-items:center;gap:8px">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        {{ $e }}
                    </div>
                @endforeach
            </div>
        @endif
        @yield('content')
    </div>
</div>
</div>

@stack('scripts')

<script>
function toggleTheme() {
    const html = document.documentElement;
    const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('ispa-theme', next);
}
</script>
</body>
</html>
