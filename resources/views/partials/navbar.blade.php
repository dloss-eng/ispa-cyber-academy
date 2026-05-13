<nav class="lnav">

    <!-- LOGO -->
    <a href="{{ route('home') }}" class="nlgo">
        <img src="{{ asset('images/logo.png') }}" alt="ISPA">
        <span>ISPA <em>CYBER</em> ACADEMY</span>
    </a>

    <!-- HAMBURGER (mobile uniquement) -->
    <button class="nav-hamburger" id="navToggle" aria-label="Menu">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <!-- LIENS -->
    <div class="nlnk" id="navMenu">
        <a href="{{ route('home') }}"
           class="{{ request()->routeIs('home') ? 'active' : '' }}">
            Accueil
        </a>

        <a href="{{ route('public.courses') }}"
           class="{{ request()->routeIs('public.courses') ? 'active' : '' }}">
            Cours
        </a>

        <a href="{{ route('contact') }}"
           class="{{ request()->routeIs('contact') ? 'active' : '' }}">
            Nous contacter
        </a>

        <a href="{{ route('about') }}"
           class="{{ request()->routeIs('about') ? 'active' : '' }}">
            A propos
        </a>

        <!-- LOGIN (visible dans menu mobile aussi) -->
        <a href="{{ route('login') }}" class="btn-cyber btn-sm nav-login-mobile">
            Se connecter
        </a>
    </div>

    <!-- THEME TOGGLE + LOGIN (desktop) -->
    <div style="display:flex;align-items:center;gap:10px">
        <button id="navThemeToggle" onclick="toggleTheme()" class="theme-btn" title="Changer le thème">
            <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
            <svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
            </svg>
        </button>
        <a href="{{ route('login') }}" class="btn-cyber btn-sm nav-login-desktop">
            Se connecter
        </a>
    </div>

</nav>

<style>
.nav-hamburger {
    display: none;
    flex-direction: column;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    z-index: 1001;
}
.nav-hamburger span {
    display: block;
    width: 25px;
    height: 2px;
    background: var(--t);
    border-radius: 2px;
    transition: all 0.3s;
}
.nav-hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.nav-hamburger.open span:nth-child(2) { opacity: 0; }
.nav-hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
.nav-login-mobile { display: none; }

@media (max-width: 768px) {
    .nav-hamburger { display: flex; }
    .nav-login-desktop { display: none !important; }
    #navThemeToggle { display: none !important; }
    .nav-login-mobile { display: inline-flex; margin-top: 8px; }
    .nlnk {
        display: none;
        flex-direction: column;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--n2, #0D1628);
        padding: 16px 24px;
        border-top: 1px solid rgba(255,255,255,0.1);
        z-index: 1000;
        gap: 4px;
    }
    .nlnk.open { display: flex; }
    .nlnk a {
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        width: 100%;
    }
}
</style>

<script>
document.getElementById('navToggle').addEventListener('click', function() {
    this.classList.toggle('open');
    document.getElementById('navMenu').classList.toggle('open');
});
</script>
