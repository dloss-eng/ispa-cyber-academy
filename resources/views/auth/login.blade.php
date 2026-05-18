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

                <input
                    type="password"
                    name="password"
                    required
                    class="fi"
                    placeholder="••••••••"
                >

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
    </script>

</body>
</html>
