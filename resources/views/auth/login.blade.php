<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — ISPA Cyber Academy</title>

    @vite([
        'resources/css/app.css'
    ])
</head>

<body>

    <div class="auth-wrapper">

        <div class="auth-card">

            <!-- HEADER -->
            <div class="auth-header">

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="ISPA"
                    class="auth-logo"
                    onerror="this.outerHTML='<div class=\'auth-logo-fallback\'>🛡️</div>'"
                >

                <div class="auth-title">
                    ISPA <span>CYBER</span> ACADEMY
                </div>

                <div class="auth-subtitle">
                    Plateforme de cybersécurité éducative
                </div>

            </div>

            <!-- FORM -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <label class="fl auth-label-top">
                    Adresse email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="fi"
                    placeholder="votre@email.com"
                >

                <label class="fl">
                    Mot de passe
                </label>

                <div style="position:relative;">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        class="fi"
                        placeholder="••••••••"
                        style="padding-right:44px;"
                    >
                    <button
                        type="button"
                        onclick="togglePasswordVisibility()"
                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;display:flex;align-items:center;color:var(--t2);"
                        aria-label="Afficher le mot de passe"
                    >
                        <svg id="eyeIconShow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg id="eyeIconHide" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                    </button>
                </div>

                <label class="auth-remember">
                    <input type="checkbox" name="remember">
                    Se souvenir de moi
                </label>

                @if($errors->any())
                    <div class="auth-error">
                        @foreach($errors->all() as $e)
                            {{ $e }}
                        @endforeach
                    </div>
                @endif

                <button type="submit" class="btn-lg">
                    🔐 Se connecter
                </button>

            </form>

            <!-- FOOTER -->
            <div class="auth-footer">
                <span>🔒 HTTPS</span>
                <span>🛡️ CSRF</span>
                <span>🔐 Bcrypt</span>
            </div>

            <!-- 🌙 Toggle Dark/Light Mode -->
            <div style="text-align:center;margin-top:16px;">
                <button onclick="toggleAuthMode()" id="auth-mode-btn"
                    style="background:none;border:1px solid var(--bd);border-radius:20px;padding:6px 16px;cursor:pointer;font-size:12px;color:var(--t2);">
                    🌙 Mode sombre
                </button>
            </div>

        </div>

    </div>

    <script>
        // Appliquer le mode sauvegardé au chargement
        (function() {
            const mode = localStorage.getItem('auth-mode') || 'dark';
            document.documentElement.setAttribute('data-theme', mode);
            updateBtn(mode);
        })();

        function toggleAuthMode() {
            const current = document.documentElement.getAttribute('data-theme') || 'dark';
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('auth-mode', next);
            updateBtn(next);
        }

        function updateBtn(mode) {
            const btn = document.getElementById('auth-mode-btn');
            if (!btn) return;
            btn.textContent = mode === 'dark' ? '☀️ Mode clair' : '🌙 Mode sombre';
        }

        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            const showIcon = document.getElementById('eyeIconShow');
            const hideIcon = document.getElementById('eyeIconHide');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            showIcon.style.display = isPassword ? 'none' : 'block';
            hideIcon.style.display = isPassword ? 'block' : 'none';
        }
    </script>

</body>
</html>