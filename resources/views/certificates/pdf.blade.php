<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
</head>
<body>

<div class="cert-wrapper">

    <div class="corner corner-tl"></div>
    <div class="corner corner-tr"></div>
    <div class="corner corner-bl"></div>
    <div class="corner corner-br"></div>

    <!-- Header -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;">
        <tr>
            <td width="60" style="vertical-align:middle; text-align:center;">
                @if($logoBase64)
                    <img src="data:image/jpeg;base64,{{ $logoBase64 }}"
                         width="52" height="52"
                         style="border-radius:50%; border:2px solid #00C896; background:#fff;">
                @endif
            </td>
            <td style="vertical-align:middle; padding-left:12px;">
                <div class="cert-academy">ISPA CYBER ACADEMY</div>
                <div class="cert-subtitle">Plateforme de cybersecurite educative</div>
            </td>
        </tr>
    </table>

    <hr style="border:none; border-top:1px solid #1A3A5C; margin-bottom:16px;">

    <div class="cert-title">Certificat de Completion</div>
    <div class="cert-declare">Ce certificat est decerne a</div>
    <div class="cert-name">{{ $certificate->user->name }}</div>
    <div class="cert-module-label">pour avoir complete avec succes le module</div>

    <table width="65%" align="center" cellpadding="10" cellspacing="0"
           style="margin:10px auto; background:#1A3A5C; border:1px solid #00C896;">
        <tr>
            <td style="text-align:center; font-family:Arial,sans-serif;
                       font-size:14px; font-weight:bold; color:#FFFFFF;">
                {{ $certificate->module->title }}
            </td>
        </tr>
    </table>

    <div class="cert-score">
        Score obtenu : <span style="color:#00C896; font-weight:bold;">{{ $certificate->final_score }}%</span>
    </div>

    <hr style="border:none; border-top:1px solid #1A3A5C; margin:16px 0;">

    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:14px;">
        <tr>
            <td width="30%" style="text-align:center;">
                <div class="cert-footer-label">Date de delivrance</div>
                <div class="cert-footer-value">{{ $certificate->issued_at->format('d/m/Y') }}</div>
            </td>
            <td width="40%" style="text-align:center;">
                <div class="cert-signature">ISPA Cyber Academy</div>
                <div class="cert-sig-label">Direction pedagogique</div>
            </td>
            <td width="30%" style="text-align:center;">
                <div class="cert-footer-label">Verifier en ligne</div>
                <div style="font-family:Arial,sans-serif; font-size:7px; color:#00C896;">
                    {{ config('app.url') }}/verifier-certificat
                </div>
            </td>
        </tr>
    </table>

    <div class="cert-code">N{{ chr(176) }} {{ $certificate->certificate_number }}</div>

</div>

<style>
    @page {
        size: 297mm 210mm;
        margin: 0;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    html, body {
        width: 297mm;
        height: 210mm;
        margin: 0;
        padding: 0;
        background: #0D1628;
        font-family: Georgia, 'Times New Roman', serif;
        color: #FFFFFF;
    }

    .cert-wrapper {
        width: 297mm;
        height: 210mm;
        background: #0D1628;
        border: 3px solid #00C896;
        position: relative;
        padding: 18px 28px;
    }

    .corner { position: absolute; width: 18px; height: 18px; }
    .corner-tl { top: 8px;  left: 8px;  border-top: 2px solid #00C896; border-left: 2px solid #00C896; }
    .corner-tr { top: 8px;  right: 8px; border-top: 2px solid #00C896; border-right: 2px solid #00C896; }
    .corner-bl { bottom: 8px; left: 8px;  border-bottom: 2px solid #00C896; border-left: 2px solid #00C896; }
    .corner-br { bottom: 8px; right: 8px; border-bottom: 2px solid #00C896; border-right: 2px solid #00C896; }

    .cert-academy {
        font-family: Arial, sans-serif; font-size: 13px;
        font-weight: bold; color: #00C896; letter-spacing: 3px;
    }
    .cert-subtitle {
        font-family: Arial, sans-serif; font-size: 9px; color: #8899AA;
    }
    .cert-title {
        font-family: Georgia, serif; font-size: 24px;
        font-weight: bold; color: #FFFFFF;
        text-align: center; margin-bottom: 8px;
    }
    .cert-declare {
        font-family: Arial, sans-serif; font-size: 10px;
        color: #8899AA; text-align: center; margin-bottom: 6px;
    }
    .cert-name {
        font-family: Georgia, serif; font-size: 28px;
        font-weight: bold; color: #00C896; text-align: center;
        border-bottom: 1px solid #00C896; padding-bottom: 4px;
        margin: 0 auto 10px auto; width: 60%;
    }
    .cert-module-label {
        font-family: Arial, sans-serif; font-size: 10px;
        color: #8899AA; text-align: center; margin-bottom: 6px;
    }
    .cert-score {
        font-family: Arial, sans-serif; font-size: 11px;
        color: #8899AA; text-align: center; margin-top: 8px;
    }
    .cert-footer-label {
        font-family: Arial, sans-serif; font-size: 9px;
        color: #8899AA; margin-bottom: 3px;
    }
    .cert-footer-value {
        font-family: Arial, sans-serif; font-size: 13px;
        font-weight: bold; color: #FFFFFF;
    }
    .cert-signature {
        font-family: Georgia, serif; font-size: 15px;
        color: #00C896; font-style: italic;
        border-bottom: 1px solid #00C896;
        padding-bottom: 3px; text-align: center; margin-bottom: 3px;
    }
    .cert-sig-label {
        font-family: Arial, sans-serif; font-size: 8px;
        color: #8899AA; text-align: center;
    }
    .cert-code {
        font-family: Arial, sans-serif; font-size: 8px;
        color: #2A4A6C; text-align: center; letter-spacing: 1px;
    }
</style>

</body>
</html>
