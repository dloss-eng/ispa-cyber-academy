<!DOCTYPE html>

<html>

    <head>
        <meta charset="UTF-8">

        {{-- 🔗 Lien vers le CSS --}}
        <link rel="stylesheet" href="{{ asset('css/certificate.css') }}">
    </head>

    <body>

        <div class="cert-border">

            <div class="certificate-icon">📜</div>

            <h1>CERTIFICAT DE COMPÉTENCES</h1>

            <div class="academy-name">
                ISPA Cyber Academy
            </div>

            <div class="text-muted mb-small">
                Ce certificat atteste que
            </div>

            <h2>{{ $certificate->user->name }}</h2>

            <div class="text-muted my-medium">
                a complété avec succès le module
            </div>

            <div class="module-title">
                {{ $certificate->module->title }}
            </div>

            <div class="info">
                Score final : <strong>{{ $certificate->final_score }}%</strong>
            </div>

            <div class="info">
                Date : {{ $certificate->issued_at->format('d/m/Y') }}
            </div>

            <div class="number">
                N° {{ $certificate->certificate_number }}
            </div>

        </div>

    </body>

</html>