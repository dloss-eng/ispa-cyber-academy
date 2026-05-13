<?php

namespace Database\Seeders;

use App\Models\{Challenge, Module};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * CtfSeeder — ISPA Cyber Academy
 * 6 challenges CTF contextualisés Côte d'Ivoire
 *
 * php artisan db:seed --class=CtfSeeder
 */
class CtfSeeder extends Seeder
{
    public function run(): void
    {
        Challenge::truncate();

        $phishingModule  = Module::where('slug', 'like', '%phishing%')->first();
        $mobileModule    = Module::where('slug', 'securite-mobile-money')->first();

        foreach ($this->challenges($phishingModule, $mobileModule) as $data) {
            Challenge::create($data);
        }

        $this->command->info('✅ 6 challenges CTF créés.');
    }

    private function challenges(?Module $phishing, ?Module $mobile): array
    {
        return [

            // ════════════════════════════════════════════════════════
            // CTF 1 — SMS Mobile Money frauduleux (flag_hunt | facile)
            // ════════════════════════════════════════════════════════
            [
                'title'        => 'Le SMS de la loterie MTN',
                'slug'         => 'sms-loterie-mtn-' . Str::random(4),
                'description'  => 'Un SMS suspect prétend que vous avez gagné une loterie MTN. '
                                . 'Analysez-le et trouvez le flag caché dans le scénario.',
                'scenario'     => <<<'HTML'
<h2>🎯 Mission : Analysez ce SMS</h2>
<p>Vous venez de recevoir le SMS suivant sur votre téléphone :</p>

<div style="background:#1a1a2e;border:2px solid #ff6b35;border-radius:12px;padding:20px;font-family:monospace;margin:20px 0">
  <p style="color:#aaa;font-size:12px;margin:0 0 8px">📱 SMS reçu — 0700-ISPA{MTN_AVERTISSEMENT}112233</p>
  <p style="color:#fff;margin:0">
    Cher client MTN CI,<br><br>
    Félicitations ! Votre numéro a été tiré au sort pour la GRANDE LOTERIE MTN 2025.<br>
    Vous avez gagné <strong style="color:#ffd700">1.500.000 FCFA</strong> !!<br><br>
    Pour récupérer vos gains, appelez IMMÉDIATEMENT notre agent au :<br>
    <strong style="color:#ff6b35">+225 07 00 11 22 33</strong><br><br>
    Offre valable 24h seulement. Ne partagez pas ce message.
  </p>
</div>

<h3>📋 Votre mission</h3>
<ol>
  <li>Identifiez les <strong>3 signaux d'alerte</strong> présents dans ce SMS</li>
  <li>Trouvez le <strong>flag caché</strong> dans le numéro de l'expéditeur</li>
  <li>Soumettez le flag au format : <code>ISPA{...}</code></li>
</ol>

<h3>💡 Indice de départ</h3>
<p>Regardez attentivement le numéro affiché dans la barre de l'expéditeur du SMS...</p>
HTML,
                'type'         => 'flag_hunt',
                'flag'         => 'ISPA{MTN_AVERTISSEMENT}',
                'hints'        => [
                    ['text' => "Le flag est caché dans le numéro de l'expéditeur du SMS.", 'cost_points' => 10],
                    ['text' => 'Format : ISPA{MOT_EN_MAJUSCULES}', 'cost_points' => 20],
                ],
                'points'       => 100,
                'difficulty'   => 'facile',
                'module_id'    => $mobile?->id,
                'is_published' => true,
                'order'        => 1,
                'max_attempts' => 0,
            ],

            // ════════════════════════════════════════════════════════
            // CTF 2 — Email phishing Orange (textual_analysis | facile)
            // ════════════════════════════════════════════════════════
            [
                'title'        => 'L\'email suspect d\'Orange Money',
                'slug'         => 'email-phishing-orange-' . Str::random(4),
                'description'  => 'Un email prétend venir du service client Orange Money. '
                                . 'Comptez les indices d\'arnaque et soumettez votre réponse.',
                'scenario'     => <<<'HTML'
<h2>🔍 Analysez cet email</h2>
<p>Voici un email reçu dans votre boîte. Votre mission : compter précisément le nombre d'indices d'arnaque.</p>

<div style="background:#0d1117;border:1px solid #333;border-radius:8px;padding:20px;font-family:monospace;font-size:13px;margin:20px 0">
  <p><strong style="color:#aaa">De :</strong> <span style="color:#ff4444">securite@orange-money-ci.verification.ml</span></p>
  <p><strong style="color:#aaa">À :</strong> vous@email.ci</p>
  <p><strong style="color:#aaa">Objet :</strong> ⚠️ URGENT : Votre compté Orange Money sera fermé dans 12 HEURES</p>
  <hr style="border-color:#333">
  <p style="color:#ddd">
    Chér(e) Client(e),<br><br>
    Nous avons detecté une activité <em>SUSPECTE</em> sur votre compté.<br>
    Pour éviter la <strong>SUSPENSION DÉFINITIVE</strong> de votre compté,<br>
    vous devez vérifier votre identité IMMÉDIATEMENT.<br><br>
    Cliquez ici : <a style="color:#4488ff" href="#">http://orange-ci-verification.ml/compte/confirmer?id=7734</a><br><br>
    Vous aurez besoin de :<br>
    - Votre numéro de téléphone<br>
    - Votre <strong style="color:#ff4444">code PIN secret</strong><br>
    - Une copie de votre CNI<br><br>
    ⏰ Délai : 12 heures à partir de maintenant.<br><br>
    Service Sécurité Orange Money CI<br>
    Tel: +225 07 88 99 00 11
  </p>
</div>

<h3>📋 Votre mission</h3>
<p>Comptez le nombre <strong>exact</strong> d'indices d'arnaque dans cet email.</p>
<p>Le flag est : <code>ISPA{NOMBRE_INDICES}</code> où NOMBRE_INDICES est le chiffre exact.</p>

<h3>Indices à rechercher</h3>
<ul>
  <li>Domaine email frauduleux</li>
  <li>Fautes d'orthographe (cherchez bien)</li>
  <li>Urgence artificielle</li>
  <li>Lien suspect</li>
  <li>Demandes d'informations sensibles (combien ?)</li>
</ul>
HTML,
                'type'         => 'textual_analysis',
                'flag'         => 'ISPA{7}',
                'hints'        => [
                    ['text' => "Comptez : domaine frauduleux (1), fautes d'orthographe (2: 'compté', 'detecté'), urgence (1), lien .ml (1), demande PIN (1), demande CNI (1) = 7 total.", 'cost_points' => 30],
                    ['text' => 'Le format est ISPA{chiffre} — un chiffre entre 5 et 10.', 'cost_points' => 15],
                ],
                'points'       => 150,
                'difficulty'   => 'facile',
                'module_id'    => $phishing?->id,
                'is_published' => true,
                'order'        => 2,
                'max_attempts' => 0,
            ],

            // ════════════════════════════════════════════════════════
            // CTF 3 — Faux profil WhatsApp (flag_hunt | moyen)
            // ════════════════════════════════════════════════════════
            [
                'title'        => 'L\'ami WhatsApp qui n\'en est pas un',
                'slug'         => 'faux-profil-whatsapp-' . Str::random(4),
                'description'  => 'Votre "ami" vous demande de l\'argent sur WhatsApp. '
                                . 'Quelque chose cloche. Trouvez le flag dans la conversation.',
                'scenario'     => <<<'HTML'
<h2>💬 Analyse de conversation WhatsApp</h2>
<p>Vous recevez les messages suivants d'un contact enregistré sous le nom <strong>"Kouamé Aya Mon Ami"</strong> :</p>

<div style="background:#075e54;border-radius:12px;padding:16px;margin:20px 0;max-width:400px">
  <div style="background:#128c7e;border-radius:8px;padding:10px;margin-bottom:8px">
    <p style="color:#fff;margin:0;font-size:13px">🟢 Kouamé Aya Mon Ami</p>
    <p style="color:#dcf8c6;margin:4px 0 0;font-size:12px">En ligne</p>
  </div>

  <div style="background:#1f2c34;border-radius:8px;padding:10px;margin-bottom:6px;max-width:85%;margin-left:auto">
    <p style="color:#e9edef;margin:0;font-size:14px">Fréro c est moi Kouamé ! J ai un souci urgent. Mon téléphone est cassé je t écris depuis celui de mon cousin.</p>
    <p style="color:#8696a0;font-size:11px;margin:4px 0 0;text-align:right">14:23 ✓✓</p>
  </div>

  <div style="background:#1f2c34;border-radius:8px;padding:10px;margin-bottom:6px;max-width:85%;margin-left:auto">
    <p style="color:#e9edef;margin:0;font-size:14px">Je suis bloqué à Bouaké j ai besoin de 35.000 FCFA MAINTENANT pour le transport. Code: ISPA{VERIFIE_TOUJOURS_PAR_APPEL}</p>
    <p style="color:#8696a0;font-size:11px;margin:4px 0 0;text-align:right">14:24 ✓✓</p>
  </div>

  <div style="background:#1f2c34;border-radius:8px;padding:10px;margin-bottom:6px;max-width:85%;margin-left:auto">
    <p style="color:#e9edef;margin:0;font-size:14px">Envoie sur Orange Money : 07 XX XX XX XX. Je te rembourse dès demain promis !</p>
    <p style="color:#8696a0;font-size:11px;margin:4px 0 0;text-align:right">14:25 ✓✓</p>
  </div>
</div>

<h3>📋 Votre mission</h3>
<p>Un vrai ami en difficulté a glissé un <strong>flag de sécurité</strong> dans sa conversation pour vous aider à vérifier l'authenticité du message.</p>
<ol>
  <li>Identifiez les <strong>signaux d'alerte</strong> dans cette conversation</li>
  <li>Trouvez le <strong>flag caché</strong> dans les messages</li>
  <li>Soumettez-le au format <code>ISPA{...}</code></li>
</ol>
HTML,
                'type'         => 'flag_hunt',
                'flag'         => 'ISPA{VERIFIE_TOUJOURS_PAR_APPEL}',
                'hints'        => [
                    ['text' => "Le flag est directement visible dans un des messages de la conversation. Lisez attentivement chaque ligne.", 'cost_points' => 15],
                    ['text' => 'Cherchez le mot "Code:" dans les messages.', 'cost_points' => 25],
                ],
                'points'       => 200,
                'difficulty'   => 'moyen',
                'module_id'    => $phishing?->id,
                'is_published' => true,
                'order'        => 3,
                'max_attempts' => 0,
            ],

            // ════════════════════════════════════════════════════════
            // CTF 4 — Faux site e-administration (textual_analysis | moyen)
            // ════════════════════════════════════════════════════════
            [
                'title'        => 'Le portail administratif fantôme',
                'slug'         => 'portail-admin-fantome-' . Str::random(4),
                'description'  => 'Un étudiant reçoit un email l\'invitant à s\'inscrire sur '
                                . 'un portail universitaire. Est-il légitime ?',
                'scenario'     => <<<'HTML'
<h2>🏛️ Vrai ou faux portail universitaire ?</h2>
<p>Un étudiant reçoit cet email concernant son inscription à l'université :</p>

<div style="background:#0d1117;border:1px solid #333;border-radius:8px;padding:20px;margin:20px 0;font-family:monospace;font-size:13px">
  <p><strong style="color:#aaa">De :</strong> <span style="color:#ff4">inscription@universite-fhb-abidjan-ci.com</span></p>
  <p><strong style="color:#aaa">Objet :</strong> Complétez votre dossier d'inscription — Action requise avant le 15 mai</p>
  <hr style="border-color:#333">
  <p style="color:#ddd">
    Cher(e) futur(e) étudiant(e),<br><br>
    Suite à votre pré-inscription, veuillez compléter votre dossier sur notre nouveau portail sécurisé :<br><br>
    <strong><a style="color:#4af" href="#">https://universite-fhb-abidjan-ci.com/inscription/2025</a></strong><br><br>
    Documents requis (à téléverser) :<br>
    ✓ Photo d'identité<br>
    ✓ Copie CNI recto-verso<br>
    ✓ Relevés de notes BAC<br>
    ✓ Paiement frais de dossier (15.000 FCFA via Mobile Money)<br><br>
    Contact : +225 05 XX XX XX XX<br>
    <em>Ce portail est agréé par le MESRS</em>
  </p>
</div>

<h3>Questions d'analyse</h3>
<p>Pour trouver le flag, répondez à cette question :</p>
<p><strong>Combien d'éléments prouvent que ce site n'est PAS l'UFHB officielle ?</strong></p>
<p>Indice sur le domaine officiel de l'UFHB : <code>ufhb.edu.ci</code></p>
<p>Soumettez : <code>ISPA{NOMBRE}</code></p>
HTML,
                'type'         => 'textual_analysis',
                'flag'         => 'ISPA{4}',
                'hints'        => [
                    ['text' => "Domaine non officiel (.com au lieu de .edu.ci), frais de dossier par Mobile Money, numéro de téléphone non vérifiable, affirmation fausse 'agréé MESRS' = 4 éléments.", 'cost_points' => 30],
                    ['text' => 'Le chiffre est entre 3 et 6.', 'cost_points' => 10],
                ],
                'points'       => 200,
                'difficulty'   => 'moyen',
                'module_id'    => null,
                'is_published' => true,
                'order'        => 4,
                'max_attempts' => 0,
            ],

            // ════════════════════════════════════════════════════════
            // CTF 5 — Chantage numérique (flag_hunt | difficile)
            // ════════════════════════════════════════════════════════
            [
                'title'        => 'Opération Chantage — Décodez le message',
                'slug'         => 'operation-chantage-' . Str::random(4),
                'description'  => 'Un message de chantage a été intercepté. '
                                . 'Déchiffrez le code caché pour identifier la bonne réaction.',
                'scenario'     => <<<'HTML'
<h2>🔐 Opération Chantage — Niveau Difficile</h2>
<p>La PLCC a intercepté ce message envoyé à une victime. Votre mission : décoder le message caché et trouver le flag.</p>

<div style="background:#1a0000;border:2px solid #cc0000;border-radius:12px;padding:20px;margin:20px 0">
  <p style="color:#ff4444;font-weight:bold;margin:0 0 12px">⚠️ MESSAGE INTERCEPTÉ — CONFIDENTIEL</p>
  <p style="color:#ffcccc;font-family:monospace;font-size:13px">
    Nous avons vos photos. Si vous ne nous envoyez pas 200.000 FCFA d'ici 48h,
    nous les partageons à tous vos contacts.<br><br>
    Envoyez sur Wave CI : 07 XX XX XX XX<br>
    Ne contactez personne. Ne portez pas plainte.<br><br>
    <span style="color:#888">
    Note secrète pour la victime (décodez en lisant une lettre sur deux à partir de la 1ère) :<br>
    <strong>PAONRETOEZRPZLZAZIIZNNZTZEEZPZLZAZCZCZ</strong>
    </span>
  </p>
</div>

<h3>📋 Votre mission</h3>
<ol>
  <li>Décodez le message caché (lisez <strong>une lettre sur deux</strong> à partir de la 1ère lettre)</li>
  <li>Le message décodé vous dira quoi faire</li>
  <li>Transformez la réponse en flag : <code>ISPA{REPONSE_EN_MAJUSCULES_SANS_ESPACES}</code></li>
</ol>

<div style="background:#0a1628;border-left:4px solid #4af;padding:16px;margin:20px 0;border-radius:4px">
  <p style="color:#adf;margin:0"><strong>Rappel :</strong> En cas de chantage numérique réel, que devez-vous faire ?</p>
  <ul style="color:#adf">
    <li>Ne jamais payer</li>
    <li>Collecter les preuves</li>
    <li>Appeler la PLCC au 1111</li>
  </ul>
</div>
HTML,
                'type'         => 'flag_hunt',
                'flag'         => 'ISPA{PORTEZPLAINTE}',
                'hints'        => [
                    ['text' => "Prenez la chaîne 'PAONRETOEZRPZLZAZIIZNNZTZEEZPZLZAZCZCZ' et lisez les lettres aux positions 1, 3, 5, 7, 9... (positions impaires).", 'cost_points' => 20],
                    ['text' => "Résultat du décodage : PORTEZPLAINTE. Le flag est ISPA{PORTEZPLAINTE}", 'cost_points' => 50],
                ],
                'points'       => 300,
                'difficulty'   => 'difficile',
                'module_id'    => null,
                'is_published' => true,
                'order'        => 5,
                'max_attempts' => 0,
            ],

            // ════════════════════════════════════════════════════════
            // CTF 6 — Investigation complète (textual_analysis | difficile)
            // ════════════════════════════════════════════════════════
            [
                'title'        => 'L\'enquête du Cyber Détective',
                'slug'         => 'enquete-cyber-detective-' . Str::random(4),
                'description'  => 'Cas complet : une victime a subi plusieurs attaques. '
                                . 'Identifiez toutes les menaces et soumettez le bon diagnostic.',
                'scenario'     => <<<'HTML'
<h2>🕵️ Enquête Cyber — Cas complet</h2>
<p>Marie-Ange, étudiante à l'UFHB, vous consulte. Elle a subi plusieurs incidents en une semaine. Analysez chaque incident et identifiez le type de menace.</p>

<h3>Incident 1 — Lundi</h3>
<div style="background:#1a1a2e;border-left:4px solid #ffd700;padding:14px;border-radius:4px;margin-bottom:16px">
  <p style="color:#fff;margin:0">Marie-Ange reçoit un SMS : "Votre compte Wave est bloqué. Envoyez votre code PIN au 0700..."</p>
</div>

<h3>Incident 2 — Mercredi</h3>
<div style="background:#1a1a2e;border-left:4px solid #ffd700;padding:14px;border-radius:4px;margin-bottom:16px">
  <p style="color:#fff;margin:0">Elle reçoit un message Facebook d'un inconnu avec des photos d'elle publiées sur sa page sans sa permission, et une menace si elle ne paye pas.</p>
</div>

<h3>Incident 3 — Vendredi</h3>
<div style="background:#1a1a2e;border-left:4px solid #ffd700;padding:14px;border-radius:4px;margin-bottom:16px">
  <p style="color:#fff;margin:0">Elle clique sur un lien "Bourse d'étude MESRS" reçu par email et remplit un formulaire avec sa CNI et ses coordonnées bancaires.</p>
</div>

<h3>📋 Votre mission</h3>
<p>Identifiez les 3 types de menaces (dans l'ordre des incidents) et construisez le flag :</p>
<p><code>ISPA{TYPE1_TYPE2_TYPE3}</code></p>
<p>Types possibles : <strong>SMISHING</strong>, <strong>PHISHING</strong>, <strong>CYBERHARCELEMENT</strong>, <strong>VISHING</strong>, <strong>ARNAQUE_MM</strong></p>

<div style="background:#0a1628;border-left:4px solid #4af;padding:16px;margin:20px 0;border-radius:4px">
  <p style="color:#adf;margin:0"><strong>Indice :</strong> Chaque incident correspond exactement à un type. Associez chaque scénario à sa définition.</p>
</div>
HTML,
                'type'         => 'textual_analysis',
                'flag'         => 'ISPA{SMISHING_CYBERHARCELEMENT_PHISHING}',
                'hints'        => [
                    ['text' => "Incident 1 : arnaque par SMS = SMISHING. Incident 2 : menace/intimidation en ligne = CYBERHARCELEMENT. Incident 3 : formulaire frauduleux par email = PHISHING.", 'cost_points' => 40],
                    ['text' => 'Format exact : ISPA{SMISHING_CYBERHARCELEMENT_PHISHING}', 'cost_points' => 60],
                ],
                'points'       => 400,
                'difficulty'   => 'difficile',
                'module_id'    => $phishing?->id,
                'is_published' => true,
                'order'        => 6,
                'max_attempts' => 0,
            ],
        ];
    }
}
