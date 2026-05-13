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

        </div>

    </div>

</body>
</html>