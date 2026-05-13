<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA</title>
    @vite(['resources/css/app.css'])
</head>

<body>

<div class="auth-2fa-wrapper">

    <div class="auth-2fa-card">

        <!-- HEADER -->
        <div class="auth-2fa-header">
            <div class="auth-2fa-icon">🔐</div>

            <div class="auth-2fa-title">
                Vérification 2 étapes
            </div>

            <div class="auth-2fa-subtitle">
                Code envoyé à votre email.
            </div>
        </div>

        <!-- DEBUG -->
        @if(config('app.debug') && isset($debugCode))
            <div class="auth-2fa-debug">
                <small>🧪 Mode démo</small>
                <div class="auth-2fa-code">
                    {{ $debugCode }}
                </div>
            </div>
        @endif

        <!-- FORM -->
        <form method="POST" action="{{ route('2fa.verify') }}">
            @csrf

            <input 
                type="text"
                name="code"
                required
                autofocus
                maxlength="6"
                class="fi auth-2fa-input"
                placeholder="000000"
            >

            @if($errors->any())
                <div class="auth-2fa-error">
                    @foreach($errors->all() as $e)
                        {{ $e }}
                    @endforeach
                </div>
            @endif

            <button type="submit" class="btn-lg">
                ✅ Vérifier
            </button>
        </form>

        <!-- RESEND -->
        <div class="auth-2fa-resend">
            <form method="POST" action="{{ route('2fa.resend') }}">
                @csrf
                <button type="submit">🔄 Renvoyer</button>
            </form>
        </div>

    </div>

</div>

</body>
</html>