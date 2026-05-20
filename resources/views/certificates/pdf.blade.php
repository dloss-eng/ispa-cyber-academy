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
        <div class="cert-header">
            <img class="cert-logo" src="data:image/jpeg;base64,{{  }}" alt="ISPA">
            <div>
                <div class="cert-academy">ISPA Cyber Academy</div>
                <div class="cert-subtitle">Plateforme de cybersécurité éducative</div>
            </div>
        </div>

        <!-- Titre -->
        <div class="cert-title">Certificat de Complétion</div>

        <!-- Séparateur -->
        <div class="cert-divider">
            <div class="cert-divider-line"></div>
            <div class="cert-divider-dot"></div>
            <div class="cert-divider-line"></div>
        </div>

        <!-- Déclaratif + Nom -->
        <div class="cert-declare">Ce certificat est décerné à</div>
        <div class="cert-name">{{ ->user->name }}</div>

        <!-- Module -->
        <div class="cert-module-label">pour avoir complété avec succès le module</div>
        <div class="cert-module-box">{{ ->module->title }}</div>

        <!-- Score -->
        <div class="cert-score">
            Score obtenu : <span class="green">{{ ->final_score }}%</span>
        </div>

        <!-- Séparateur -->
        <div class="cert-divider">
            <div class="cert-divider-line"></div>
        </div>

        <!-- Footer -->
        <div class="cert-footer">

            <!-- Date -->
            <div class="cert-footer-block">
                <div class="cert-footer-label">Date de délivrance</div>
                <div class="cert-footer-value">{{ ->issued_at->format('d/m/Y') }}</div>
            </div>

            <!-- Signature -->
            <div class="cert-footer-block">
                <div class="cert-signature">ISPA Cyber Academy</div>
                <div class="cert-sig-label">Direction pédagogique</div>
            </div>

            <!-- QR -->
            <div class="cert-qr">
                <div class="cert-qr-box">🔗</div>
                <div class="cert-qr-label">Vérifier</div>
            </div>

        </div>

        <!-- Code unique -->
        <div class="cert-code">
            N° {{ ->certificate_number }} &nbsp;•&nbsp; {{ config('app.url') }}/verifier-certificat
        </div>

    </div>
<style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: #0D1628;
            color: #FFFFFF;
            width: 210mm;
            height: 148mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cert-wrapper {
            width: 200mm;
            height: 138mm;
            background: #0D1628;
            border: 2px solid #00C896;
            border-radius: 8px;
            position: relative;
            padding: 8mm 12mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        /* Bordure intérieure */
        .cert-wrapper::before {
            content: '';
            position: absolute;
            inset: 4px;
            border: 0.5px solid #1A3A5C;
            border-radius: 6px;
            pointer-events: none;
        }

        /* Coins décoratifs */
        .corner { position: absolute; width: 16px; height: 16px; }
        .corner-tl { top: 8px; left: 8px; border-top: 2px solid #00C896; border-left: 2px solid #00C896; }
        .corner-tr { top: 8px; right: 8px; border-top: 2px solid #00C896; border-right: 2px solid #00C896; }
        .corner-bl { bottom: 8px; left: 8px; border-bottom: 2px solid #00C896; border-left: 2px solid #00C896; }
        .corner-br { bottom: 8px; right: 8px; border-bottom: 2px solid #00C896; border-right: 2px solid #00C896; }

        /* Header */
        .cert-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2mm;
        }

        .cert-logo {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 1.5px solid #00C896;
            background: #FFFFFF;
            object-fit: cover;
        }

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

        /* Titre principal */
        .cert-title {
            font-family: Georgia, serif;
            font-size: 20px;
            font-weight: bold;
            color: #FFFFFF;
            text-align: center;
            letter-spacing: 1px;
        }

        /* Séparateur */
        .cert-divider {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 1mm 0;
        }
        .cert-divider-line {
            flex: 1;
            height: 0.5px;
            background: #1A3A5C;
        }
        .cert-divider-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #00C896;
        }

        /* Déclaratif */
        .cert-declare {
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #8899AA;
            text-align: center;
        }

        /* Nom apprenant */
        .cert-name {
            font-family: Georgia, serif;
            font-size: 22px;
            font-weight: bold;
            color: #00C896;
            text-align: center;
            border-bottom: 0.5px solid #00C896;
            padding-bottom: 2px;
            width: 80%;
        }

        /* Module */
        .cert-module-label {
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #8899AA;
            text-align: center;
        }

        .cert-module-box {
            background: #1A3A5C;
            border: 0.5px solid #00C896;
            border-radius: 4px;
            padding: 4px 20px;
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-weight: bold;
            color: #FFFFFF;
            text-align: center;
            max-width: 90%;
        }

        /* Infos score */
        .cert-score {
            font-family: Arial, sans-serif;
            font-size: 9px;
            color: #8899AA;
            text-align: center;
        }
        .cert-score span.green { color: #00C896; font-weight: bold; }
        .cert-score span.orange { color: #FF6B35; font-weight: bold; }

        /* Footer */
        .cert-footer {
            width: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
        }

        .cert-footer-block {
            text-align: center;
        }

        .cert-footer-label {
            font-family: Arial, sans-serif;
            font-size: 8px;
            color: #8899AA;
        }

        .cert-footer-value {
            font-family: Arial, sans-serif;
            font-size: 10px;
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

        /* QR code area */
        .cert-qr {
            text-align: center;
        }
        .cert-qr-box {
            width: 36px;
            height: 36px;
            background: #1A3A5C;
            border: 0.5px solid #00C896;
            border-radius: 3px;
            margin: 0 auto 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .cert-qr-label {
            font-family: Arial, sans-serif;
            font-size: 7px;
            color: #8899AA;
        }

        /* Code unique */
        .cert-code {
            font-family: Arial, sans-serif;
            font-size: 7px;
            color: #1A3A5C;
            text-align: center;
            letter-spacing: 1px;
        }
    </style>
</body>
</html>
