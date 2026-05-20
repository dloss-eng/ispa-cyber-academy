<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
</head>
<body>

<div class="cert-wrapper">

    <!-- Coins décoratifs -->
    <div class="corner corner-tl"></div>
    <div class="corner corner-tr"></div>
    <div class="corner corner-bl"></div>
    <div class="corner corner-br"></div>

    <!-- Header : Logo + Nom plateforme -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
        <tr>
            <td width="56" style="vertical-align:middle; text-align:center;">
                @if($logoBase64)
                    <img src="data:image/jpeg;base64,{{ $logoBase64 }}"
                         width="46" height="46"
                         style="border-radius:50%; border:2px solid #00C896; background:#fff;">
                @else
                    <div style="font-size:28px; text-align:center;">🛡️</div>
                @endif
            </td>
            <td style="vertical-align:middle; padding-left:10px;">
                <div class="cert-academy">ISPA Cyber Academy</div>
                <div class="cert-subtitle">Plateforme de cybersécurité éducative</div>
            </td>
        </tr>
    </table>

    <!-- Séparateur -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:8px;">
        <tr>
            <td><hr style="border:none; border-top:0.5px solid #1A3A5C; margin:0;"></td>
            <td width="12" style="text-align:center; color:#00C896; font-size:8px;">◆</td>
            <td><hr style="border:none; border-top:0.5px solid #1A3A5C; margin:0;"></td>
        </tr>
    </table>

    <!-- Titre -->
    <div class="cert-title">Certificat de Complétion</div>

    <!-- Déclaratif -->
    <div class="cert-declare">Ce certificat est décerné à</div>

    <!-- Nom apprenant -->
    <div class="cert-name">{{ $certificate->user->name }}</div>

    <!-- Module -->
    <div class="cert-module-label">pour avoir complété avec succès le module</div>

    <table width="70%" align="center" cellpadding="6" cellspacing="0" style="margin:6px auto;">
        <tr>
            <td class="cert-module-box">
                {{ $certificate->module->title }}
            </td>
        </tr>
    </table>

    <!-- Score -->
    <div class="cert-score">
        Score obtenu : <span style="color:#00C896; font-weight:bold;">{{ $certificate->final_score }}%</span>
    </div>

    <!-- Séparateur -->
    <hr style="border:none; border-top:0.5px solid #1A3A5C; margin:8px 0;">

    <!-- Footer -->
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <!-- Date -->
            <td width="30%" style="text-align:center; vertical-align:bottom;">
                <div class="cert-footer-label">Date de délivrance</div>
                <div class="cert-footer-value">{{ $certificate->issued_at->format('d/m/Y') }}</div>
            </td>

            <!-- Signature -->
            <td width="40%" style="text-align:center; vertical-align:bottom;">
                <div class="cert-signature">ISPA Cyber Academy</div>
                <div class="cert-sig-label">Direction pédagogique</div>
            </td>

            <!-- Vérification -->
            <td width="30%" style="text-align:center; vertical-align:bottom;">
                <div class="cert-footer-label">Vérifier en ligne</div>
                <div style="font-family:Arial,sans-serif; font-size:7px; color:#00C896;">
                    {{ config('app.url') }}/verifier-certificat
                </div>
            </td>
        </tr>
    </table>

    <!-- Code unique -->
    <div class="cert-code">
        N° {{ $certificate->certificate_number }}
    </div>

</div>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: Georgia, 'Times New Roman', serif;
        background: #0D1628;
        color: #FFFFFF;
        padding: 10px;
    }

    .cert-wrapper {
        background: #0D1628;
        border: 2px solid #00C896;
        border-radius: 8px;
        position: relative;
        padding: 14px 20px;
        width: 100%;
    }

    /* Coins décoratifs */
    .corner { position: absolute; width: 14px; height: 14px; }
    .corner-tl { top: 6px; left: 6px; border-top: 2px solid #00C896; border-left: 2px solid #00C896; }
    .corner-tr { top: 6px; right: 6px; border-top: 2px solid #00C896; border-right: 2px solid #00C896; }
    .corner-bl { bottom: 6px; left: 6px; border-bottom: 2px solid #00C896; border-left: 2px solid #00C896; }
    .corner-br { bottom: 6px; right: 6px; border-bottom: 2px solid #00C896; border-right: 2px solid #00C896; }

    .cert-academy {
        font-family: Arial, sans-serif;
        font-size: 13px;
        font-weight: bold;
        color: #00C896;
        letter-spacing: 3px;
        text-transform: uppercase;
    }

    .cert-subtitle {
        font-family: Arial, sans-serif;
        font-size: 9px;
        color: #8899AA;
        letter-spacing: 1px;
    }

    .cert-title {
        font-family: Georgia, serif;
        font-size: 20px;
        font-weight: bold;
        color: #FFFFFF;
        text-align: center;
        letter-spacing: 1px;
        margin-bottom: 6px;
    }

    .cert-declare {
        font-family: Arial, sans-serif;
        font-size: 9px;
        color: #8899AA;
        text-align: center;
        margin-bottom: 4px;
    }

    .cert-name {
        font-family: Georgia, serif;
        font-size: 22px;
        font-weight: bold;
        color: #00C896;
        text-align: center;
        border-bottom: 0.5px solid #00C896;
        padding-bottom: 3px;
        margin: 0 auto 6px auto;
        width: 70%;
    }

    .cert-module-label {
        font-family: Arial, sans-serif;
        font-size: 9px;
        color: #8899AA;
        text-align: center;
        margin-bottom: 4px;
    }

    .cert-module-box {
        background: #1A3A5C;
        border: 0.5px solid #00C896;
        border-radius: 4px;
        font-family: Arial, sans-serif;
        font-size: 12px;
        font-weight: bold;
        color: #FFFFFF;
        text-align: center;
        padding: 6px 16px;
    }

    .cert-score {
        font-family: Arial, sans-serif;
        font-size: 10px;
        color: #8899AA;
        text-align: center;
        margin-top: 4px;
    }

    .cert-footer-label {
        font-family: Arial, sans-serif;
        font-size: 8px;
        color: #8899AA;
    }

    .cert-footer-value {
        font-family: Arial, sans-serif;
        font-size: 11px;
        font-weight: bold;
        color: #FFFFFF;
    }

    .cert-signature {
        font-family: Georgia, serif;
        font-size: 13px;
        color: #00C896;
        font-style: italic;
        border-bottom: 0.5px solid #00C896;
        padding-bottom: 2px;
        text-align: center;
    }

    .cert-sig-label {
        font-family: Arial, sans-serif;
        font-size: 8px;
        color: #8899AA;
        text-align: center;
        margin-top: 2px;
    }

    .cert-code {
        font-family: Arial, sans-serif;
        font-size: 7px;
        color: #2A4A6C;
        text-align: center;
        letter-spacing: 1px;
        margin-top: 8px;
    }
</style>

</body>
</html>
