<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>403</title>

    @vite(['resources/css/app.css'])

</head>

<body>

    <div class="error-container">

        <div class="error-box">

            <div class="error-icon">🔒</div>

            <div class="error-code">403</div>

            <div class="error-message">
                Accès refusé.
            </div>

            {{-- ✅ Formulaire de déconnexion automatique --}}
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>

            <a href="#" class="error-link"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                ← Retour
            </a>

        </div>

    </div>

</body>
</html>
