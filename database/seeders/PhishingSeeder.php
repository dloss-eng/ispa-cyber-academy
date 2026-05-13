<?php

namespace Database\Seeders;

use App\Models\{Module, Lesson, Quiz, Question, Answer};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * PhishingSeeder — ISPA Cyber Academy
 *
 * Enrichit le module "Le Phishing" avec 4 leçons complètes et leurs quiz.
 *
 * Usage :
 *   php artisan db:seed --class=PhishingSeeder
 *
 * ⚠️  Ce seeder trouve le module par slug 'le-phishing' (ou le crée).
 *     Si votre slug est différent, ajustez la constante MODULE_SLUG.
 */
class PhishingSeeder extends Seeder
{
    private const MODULE_SLUG = 'le-phishing';

    // ──────────────────────────────────────────────────────────────
    public function run(): void
    {
        // Trouver ou créer le module
        $module = Module::firstOrCreate(
            ['slug' => self::MODULE_SLUG],
            [
                'title'          => 'Le Phishing',
                'description'    => 'Le phishing, appelé aussi hameçonnage, est une technique de cyberattaque '
                                  . 'utilisée pour voler vos informations personnelles. Apprenez à le '
                                  . 'reconnaître et à vous en protéger dans le contexte ivoirien.',
                'level'          => 'tous',
                'order'          => 3,
                'duration_hours' => 2,
                'is_published'   => true,
            ]
        );

        // Supprimer les leçons existantes pour repartir proprement
        $module->lessons()->each(function ($lesson) {
            $lesson->quizzes()->each(function ($quiz) {
                $quiz->questions()->each(fn($q) => $q->answers()->delete());
                $quiz->questions()->delete();
                $quiz->delete();
            });
            $lesson->delete();
        });

        // Créer les leçons
        foreach ($this->lessonsData() as $lessonData) {
            $quizData = $lessonData['quiz'] ?? null;
            unset($lessonData['quiz']);

            $lessonData['module_id'] = $module->id;
            $lesson = Lesson::create($lessonData);

            if ($quizData) {
                $this->createQuiz($lesson, $quizData);
            }
        }

        $this->command->info("✅  Module \"{$module->title}\" enrichi avec " . $module->lessons()->count() . " leçons.");
    }

    // ──────────────────────────────────────────────────────────────
    private function createQuiz(Lesson $lesson, array $quizData): void
    {
        $questions = $quizData['questions'];
        unset($quizData['questions']);

        $quizData['lesson_id'] = $lesson->id;
        $quiz = Quiz::create($quizData);

        foreach ($questions as $i => $qData) {
            $answers = $qData['answers'];
            $question = Question::create([
                'quiz_id'       => $quiz->id,
                'question_text' => $qData['text'],
                'type'          => $qData['type'],
                'explanation'   => $qData['explanation'] ?? null,
                'points'        => $qData['points'] ?? 1,
                'order'         => $i + 1,
            ]);

            foreach ($answers as $j => $aData) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $aData['text'],
                    'is_correct'  => $aData['correct'],
                    'order'       => $j + 1,
                ]);
            }
        }
    }

    // ──────────────────────────────────────────────────────────────
    // CONTENU DES LEÇONS
    // ──────────────────────────────────────────────────────────────
    private function lessonsData(): array
    {
        return [

            // ══════════════════════════════════════════════════════
            // LEÇON 1 — Qu'est-ce que le phishing ?
            // ══════════════════════════════════════════════════════
            [
                'title'            => "Qu'est-ce que le phishing ?",
                'slug'             => 'phishing-introduction-' . Str::random(4),
                'content'          => <<<'HTML'
<h2>Qu'est-ce que le phishing ?</h2>

<p>Le <strong>phishing</strong> (ou hameçonnage) est une technique d'escroquerie numérique où un cybercriminel
se fait passer pour une personne ou une organisation de confiance afin de vous voler des informations
personnelles : mot de passe, numéro de carte bancaire, code Mobile Money, etc.</p>

<h3>L'image du pêcheur</h3>
<p>Le terme vient de l'anglais <em>fishing</em> (pêche) : le cybercriminel lance un « hameçon » en espérant
que quelqu'un « morde ». Il envoie des messages en masse et attend que des victimes cliquent ou répondent.</p>

<h3>Les 3 formes principales</h3>
<ul>
  <li><strong>Email phishing</strong> — faux emails de banques, d'opérateurs téléphoniques ou de services en ligne</li>
  <li><strong>Smishing (SMS)</strong> — messages frauduleux prétendant venir d'Orange, MTN, Wave, etc.</li>
  <li><strong>Vishing (appel vocal)</strong> — appel d'un faux agent qui demande vos informations</li>
</ul>

<h3>Comment ça se passe concrètement ?</h3>
<ol>
  <li>Vous recevez un message semblant venir d'une source fiable</li>
  <li>Le message crée une urgence : « Votre compte sera bloqué ! », « Vous avez gagné ! »</li>
  <li>Vous êtes invité à cliquer sur un lien ou à communiquer un code</li>
  <li>Vos informations sont volées</li>
</ol>

<h3>Exemple réel en Côte d'Ivoire</h3>
<p style="background:#1a2e1a;border-left:4px solid #ff6b35;padding:16px;border-radius:4px;font-family:monospace">
📱 <strong>SMS reçu :</strong><br>
« Cher client MTN, votre compte MoMo a été suspendu.
Veuillez confirmer votre identité en appelant le 0700112233 ou en envoyant votre code PIN au 900. »
</p>
<p>⚠️ <strong>C'est une arnaque.</strong> MTN ne demande jamais votre code PIN par SMS ou téléphone.</p>
HTML
                ,
                'video_url'        => 'https://www.youtube.com/watch?v=Y7zNlEMDmI4',
                'order'            => 1,
                'duration_minutes' => 12,
                'is_published'     => true,
                'quiz' => [
                    'title'              => "Quiz 1 — Comprendre le phishing",
                    'description'        => "Vérifiez votre compréhension des bases du phishing.",
                    'passing_score'      => 70,
                    'time_limit_minutes' => 10,
                    'max_attempts'       => 3,
                    'is_published'       => true,
                    'questions' => [
                        [
                            'text'        => "Que signifie le terme « phishing » ?",
                            'type'        => 'qcm',
                            'explanation' => "Le phishing vient de « fishing » (pêche) : l'escroc lance un hameçon numérique pour piéger ses victimes.",
                            'points'      => 1,
                            'answers'     => [
                                ['text' => "Une technique pour voler des informations en se faisant passer pour une entité de confiance", 'correct' => true],
                                ['text' => "Un virus informatique qui détruit les fichiers", 'correct' => false],
                                ['text' => "Un logiciel de protection contre les hackers", 'correct' => false],
                                ['text' => "Une méthode de sauvegarde de données", 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "Quelle est la technique de phishing par SMS ?",
                            'type'        => 'qcm',
                            'explanation' => "Le smishing (SMS + phishing) utilise des messages texte frauduleux.",
                            'points'      => 1,
                            'answers'     => [
                                ['text' => "Phishing", 'correct' => false],
                                ['text' => "Smishing", 'correct' => true],
                                ['text' => "Vishing", 'correct' => false],
                                ['text' => "Hacking", 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "MTN peut légitimement vous demander votre code PIN par SMS.",
                            'type'        => 'vrai_faux',
                            'explanation' => "FAUX. Aucun opérateur légitime (MTN, Orange, Wave…) ne vous demandera jamais votre code PIN ou mot de passe par SMS, email ou appel.",
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                        [
                            'text'        => "Lequel de ces éléments est un signal d'alerte typique d'un message de phishing ?",
                            'type'        => 'qcm',
                            'explanation' => "La création d'une urgence artificielle ('compte bloqué dans 24h') est la tactique la plus commune pour pousser à agir sans réfléchir.",
                            'points'      => 2,
                            'answers'     => [
                                ['text' => "Le message est écrit en français correct", 'correct' => false],
                                ['text' => "Le message vient d'un contact connu", 'correct' => false],
                                ['text' => "Le message crée une urgence : 'votre compte sera fermé dans 24h'", 'correct' => true],
                                ['text' => "Le message ne contient pas de lien", 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "Le phishing touche uniquement les ordinateurs, pas les téléphones mobiles.",
                            'type'        => 'vrai_faux',
                            'explanation' => "FAUX. Le phishing via SMS et applications de messagerie (WhatsApp, Telegram) est très répandu et particulièrement efficace sur mobile.",
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                    ],
                ],
            ],

            // ══════════════════════════════════════════════════════
            // LEÇON 2 — Phishing sur WhatsApp et réseaux sociaux
            // ══════════════════════════════════════════════════════
            [
                'title'            => "Phishing sur WhatsApp et réseaux sociaux",
                'slug'             => 'phishing-whatsapp-reseaux-' . Str::random(4),
                'content'          => <<<'HTML'
<h2>Le phishing sur WhatsApp et réseaux sociaux</h2>

<p>En Côte d'Ivoire, <strong>WhatsApp et Facebook</strong> sont les canaux les plus utilisés
pour les arnaques de phishing. Les escrocs exploitent la confiance que vous avez envers
vos contacts et les groupes que vous fréquentez.</p>

<h3>Les techniques les plus courantes</h3>

<h4>1. L'usurpation d'identité WhatsApp</h4>
<p>L'escroc pirate le compte d'un de vos amis ou crée un faux profil avec sa photo et son nom.
Il vous envoie ensuite un message pour vous demander de l'argent via Mobile Money en
prétextant une urgence.</p>
<p style="background:#1a1a2e;border-left:4px solid #4b7bff;padding:16px;border-radius:4px;font-family:monospace">
💬 <strong>Message WhatsApp reçu (de votre ami "Kouamé") :</strong><br>
« Mon frère, j'ai un problème urgent. Je suis bloqué à Yamoussoukro. Peux-tu m'envoyer 25.000 FCFA sur Orange Money ? Je te rembourse demain. »
</p>
<p>⚠️ <strong>Réflexe :</strong> Appelez directement votre ami avant d'envoyer quoi que ce soit !</p>

<h4>2. Les liens de phishing dans les groupes</h4>
<p>Un message est partagé dans un groupe WhatsApp avec un lien vers un faux site qui promet
des cadeaux, des bourses, ou des emplois. En cliquant, vous installez un logiciel malveillant
ou vous donnez vos informations.</p>

<h4>3. Les faux concours Facebook</h4>
<p>Une page Facebook imite une entreprise connue (MTN, Orange, Moov…) et annonce un concours :
« Likez et partagez pour gagner un smartphone ! » L'objectif est de collecter vos données
personnelles ou de vous diriger vers un site frauduleux.</p>

<h3>Comment vérifier un message suspect ?</h3>
<ul>
  <li>✅ <strong>Appelez directement</strong> la personne par téléphone</li>
  <li>✅ <strong>Vérifiez le numéro</strong> d'où vient le message</li>
  <li>✅ <strong>Ne cliquez jamais</strong> sur un lien dans un message non sollicité</li>
  <li>✅ <strong>Vérifiez l'URL</strong> : une page officielle d'Orange CI sera sur orange.ci, pas sur orange-ci-cadeaux.ml</li>
</ul>

<h3>Que faire si vous avez été victime ?</h3>
<ol>
  <li>Changez immédiatement votre mot de passe WhatsApp/Facebook</li>
  <li>Prévenez vos contacts que votre compte a peut-être été piraté</li>
  <li>Contactez le service client de l'opérateur concerné</li>
  <li>Signalez l'incident sur la plateforme ISPA (bouton « Signaler »)</li>
</ol>
HTML
                ,
                'video_url'        => null,
                'order'            => 2,
                'duration_minutes' => 14,
                'is_published'     => true,
                'quiz' => [
                    'title'              => "Quiz 2 — Phishing sur WhatsApp et réseaux sociaux",
                    'description'        => "Testez votre capacité à identifier les arnaques sur les réseaux sociaux.",
                    'passing_score'      => 70,
                    'time_limit_minutes' => 10,
                    'max_attempts'       => 3,
                    'is_published'       => true,
                    'questions' => [
                        [
                            'text'        => "Votre ami vous demande 30.000 FCFA via WhatsApp en urgence. Quelle est la bonne réaction ?",
                            'type'        => 'qcm',
                            'explanation' => "L'usurpation d'identité WhatsApp est très courante. Toujours appeler directement pour vérifier avant d'envoyer de l'argent.",
                            'points'      => 2,
                            'answers'     => [
                                ['text' => "J'envoie immédiatement car c'est mon ami", 'correct' => false],
                                ['text' => "J'appelle mon ami directement pour vérifier", 'correct' => true],
                                ['text' => "Je lui envoie ma photo d'identité pour confirmer", 'correct' => false],
                                ['text' => "Je transfère le message à d'autres amis", 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "Un groupe WhatsApp partage un lien promettant 50.000 FCFA de la part de MTN. Que faites-vous ?",
                            'type'        => 'qcm',
                            'explanation' => "MTN ne distribue pas d'argent via des liens WhatsApp. Les offres trop belles pour être vraies sont presque toujours des arnaques.",
                            'points'      => 2,
                            'answers'     => [
                                ['text' => "Je clique immédiatement", 'correct' => false],
                                ['text' => "Je partage dans tous mes groupes pour que mes amis profitent", 'correct' => false],
                                ['text' => "Je ne clique pas et j'avertis le groupe que c'est probablement une arnaque", 'correct' => true],
                                ['text' => "J'envoie mon numéro de téléphone pour participer", 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "Un escroc peut créer un faux profil WhatsApp avec la photo d'un de vos amis.",
                            'type'        => 'vrai_faux',
                            'explanation' => "VRAI. Les escrocs créent facilement de faux profils en copiant la photo de profil et le nom. Un appel vocal ou vidéo permet de vérifier.",
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => true],
                                ['text' => 'Faux', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "Comment reconnaître une page Facebook officielle d'une entreprise ?",
                            'type'        => 'qcm',
                            'explanation' => "Le badge bleu de vérification (✓) est le seul indicateur fiable d'une page officielle sur Facebook.",
                            'points'      => 1,
                            'answers'     => [
                                ['text' => "Elle a beaucoup de likes", 'correct' => false],
                                ['text' => "Elle a le badge bleu de vérification", 'correct' => true],
                                ['text' => "Elle propose des cadeaux", 'correct' => false],
                                ['text' => "Elle poste souvent", 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "Si vous cliquez par erreur sur un lien suspect, la première chose à faire est de :",
                            'type'        => 'qcm',
                            'explanation' => "Changer immédiatement vos mots de passe limite les dégâts si vos identifiants ont été capturés.",
                            'points'      => 2,
                            'answers'     => [
                                ['text' => "Ne rien faire et attendre", 'correct' => false],
                                ['text' => "Changer immédiatement vos mots de passe et contacter le service client", 'correct' => true],
                                ['text' => "Éteindre votre téléphone définitivement", 'correct' => false],
                                ['text' => "Partager le lien pour prévenir vos amis", 'correct' => false],
                            ],
                        ],
                    ],
                ],
            ],

            // ══════════════════════════════════════════════════════
            // LEÇON 3 — Reconnaître un email de phishing
            // ══════════════════════════════════════════════════════
            [
                'title'            => "Reconnaître un email de phishing",
                'slug'             => 'reconnaitre-email-phishing-' . Str::random(4),
                'content'          => <<<'HTML'
<h2>Comment reconnaître un email de phishing ?</h2>

<p>Même si WhatsApp et les SMS sont les vecteurs principaux en Côte d'Ivoire,
les <strong>emails frauduleux</strong> sont de plus en plus utilisés, notamment dans
le milieu professionnel et universitaire.</p>

<h3>Les 6 signaux d'alerte à retenir</h3>

<table>
  <thead>
    <tr>
      <th>Signal</th>
      <th>Exemple concret</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>1. Adresse expéditeur suspecte</strong></td>
      <td>service-orange@gmail.com au lieu de service@orange.ci</td>
    </tr>
    <tr>
      <td><strong>2. Urgence artificielle</strong></td>
      <td>« Votre compte sera fermé dans 48h si vous n'agissez pas »</td>
    </tr>
    <tr>
      <td><strong>3. Fautes d'orthographe</strong></td>
      <td>« Votre compté a été suspendu » (accent sur le 'e' de compte)</td>
    </tr>
    <tr>
      <td><strong>4. Lien suspect</strong></td>
      <td>http://orange-verification-ci.ml/connexion</td>
    </tr>
    <tr>
      <td><strong>5. Demande d'informations sensibles</strong></td>
      <td>« Saisissez votre mot de passe pour confirmer »</td>
    </tr>
    <tr>
      <td><strong>6. Pièce jointe non attendue</strong></td>
      <td>Fichier « Facture.pdf.exe » ou « Contrat.docx »</td>
    </tr>
  </tbody>
</table>

<h3>Analyser un lien avant de cliquer</h3>
<p>Passez votre souris sur le lien (sans cliquer) pour voir l'URL réelle :</p>
<ul>
  <li>✅ <strong>Légitime :</strong> https://www.orange.ci/espace-client</li>
  <li>❌ <strong>Frauduleux :</strong> http://orange-ci-securite.ga/login</li>
  <li>❌ <strong>Frauduleux :</strong> https://orange.ci.verifier-compte.ru/</li>
</ul>
<p>⚠️ <strong>Astuce :</strong> Le vrai domaine est toujours la partie juste avant le premier « / » après « https:// ».</p>

<h3>Exemple d'email de phishing type</h3>
<div style="background:#1a1a1a;border:1px solid #333;border-radius:8px;padding:20px;font-family:monospace;font-size:13px">
  <p><strong>De :</strong> <span style="color:#ff6b6b">securite-compte@orange-ci-alert.com</span><br>
  <strong>Objet :</strong> ⚠️ Activité suspecte détectée sur votre compte Orange Money</p>
  <hr style="border-color:#333">
  <p>Cher(e) client(e),</p>
  <p>Nous avons détecté une <em>activité suspecte</em> sur votre compte Orange Money.
  Pour <strong>protéger votre argent</strong>, veuillez vérifier votre identité immédiatement
  en cliquant sur le lien ci-dessous :</p>
  <p><a href="#" style="color:#4b7bff">http://orange-verification-secure.ml/compte/verifier</a></p>
  <p>⏰ Vous avez <strong>24 heures</strong> pour agir, après quoi votre compte sera suspendu.</p>
</div>
<p>🔍 <strong>Indices d'arnaque dans cet email :</strong></p>
<ul>
  <li>Domaine expéditeur : <code>orange-ci-alert.com</code> → pas officiel</li>
  <li>Urgence de 24h → pression psychologique</li>
  <li>Lien vers <code>.ml</code> → domaine malien, pas Orange CI</li>
  <li>Demande de vérification d'identité → vol de données</li>
</ul>
HTML
                ,
                'video_url'        => null,
                'order'            => 3,
                'duration_minutes' => 15,
                'is_published'     => true,
                'quiz' => [
                    'title'              => "Quiz 3 — Analyser un email suspect",
                    'description'        => "Entraînez-vous à détecter les faux emails.",
                    'passing_score'      => 70,
                    'time_limit_minutes' => 12,
                    'max_attempts'       => 3,
                    'is_published'       => true,
                    'questions' => [
                        [
                            'text'        => "Quel est l'indice le plus fiable pour identifier un email frauduleux ?",
                            'type'        => 'qcm',
                            'explanation' => "L'adresse email de l'expéditeur est le signal le plus révélateur. Un email d'Orange CI viendra forcément de @orange.ci, jamais de @gmail.com ou d'un autre domaine.",
                            'points'      => 2,
                            'answers'     => [
                                ['text' => "La longueur de l'email", 'correct' => false],
                                ['text' => "L'adresse email de l'expéditeur", 'correct' => true],
                                ['text' => "La présence d'images", 'correct' => false],
                                ['text' => "L'heure d'envoi", 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "Un email de 'service@orange.ci.verifier.ru' est-il un email officiel d'Orange CI ?",
                            'type'        => 'vrai_faux',
                            'explanation' => "FAUX. Le vrai domaine est la partie après le dernier '@' : ici c'est 'verifier.ru' (domaine russe), pas 'orange.ci'. L'escroc ajoute 'orange.ci' au milieu pour tromper.",
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                        [
                            'text'        => "Que doit-on faire avant de cliquer sur un lien dans un email ?",
                            'type'        => 'qcm',
                            'explanation' => "Passer la souris sur le lien (sans cliquer) révèle l'URL réelle. Sur mobile, appuyer longtemps sur le lien affiche l'URL complète.",
                            'points'      => 1,
                            'answers'     => [
                                ['text' => "Cliquer rapidement", 'correct' => false],
                                ['text' => "Passer la souris sur le lien pour voir l'URL réelle", 'correct' => true],
                                ['text' => "Copier le lien et l'envoyer à un ami", 'correct' => false],
                                ['text' => "Répondre à l'email pour demander confirmation", 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "Une entreprise légitime peut vous demander votre mot de passe par email.",
                            'type'        => 'vrai_faux',
                            'explanation' => "FAUX. Aucune entreprise sérieuse (banque, opérateur, réseau social) ne demandera jamais votre mot de passe par email. C'est un signal d'arnaque garanti.",
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                        [
                            'text'        => "Parmi ces domaines, lequel est potentiellement légitime pour Orange Côte d'Ivoire ?",
                            'type'        => 'qcm',
                            'explanation' => "orange.ci est le domaine officiel d'Orange en Côte d'Ivoire. Les autres sont des domaines frauduleux imitant le nom.",
                            'points'      => 2,
                            'answers'     => [
                                ['text' => "orange-ci-securite.ml", 'correct' => false],
                                ['text' => "orange.ci", 'correct' => true],
                                ['text' => "orange-verification.ga", 'correct' => false],
                                ['text' => "orangemoney-ci.com.ru", 'correct' => false],
                            ],
                        ],
                    ],
                ],
            ],

            // ══════════════════════════════════════════════════════
            // LEÇON 4 — Se protéger et réagir face au phishing
            // ══════════════════════════════════════════════════════
            [
                'title'            => "Se protéger et réagir face au phishing",
                'slug'             => 'se-proteger-phishing-' . Str::random(4),
                'content'          => <<<'HTML'
<h2>Comment se protéger du phishing et réagir en cas d'attaque ?</h2>

<p>Savoir identifier le phishing c'est bien. Savoir s'en protéger et réagir,
c'est encore mieux. Voici les <strong>règles d'or</strong> à appliquer au quotidien.</p>

<h3>Les 5 règles de protection</h3>

<h4>Règle 1 — Activez la double authentification (2FA)</h4>
<p>La <strong>vérification en deux étapes</strong> ajoute un code temporaire après votre mot de passe.
Même si un escroc vole votre mot de passe, il ne peut pas accéder à votre compte sans ce code.</p>
<ul>
  <li>WhatsApp : Paramètres → Compte → Vérification en deux étapes</li>
  <li>Facebook : Paramètres → Sécurité → Authentification à deux facteurs</li>
  <li>Gmail : Mon compte Google → Sécurité → Validation en 2 étapes</li>
</ul>

<h4>Règle 2 — Ne cliquez jamais sans vérifier</h4>
<p>Avant de cliquer sur un lien :</p>
<ul>
  <li>Vérifiez l'URL complète</li>
  <li>Tapez directement l'adresse dans votre navigateur plutôt que de cliquer</li>
  <li>En cas de doute, allez sur le site officiel par vos propres moyens</li>
</ul>

<h4>Règle 3 — Ne partagez jamais vos codes secrets</h4>
<p>Aucun opérateur (MTN, Orange, Wave, Moov) ne vous demandera jamais :</p>
<ul>
  <li>Votre code PIN Mobile Money</li>
  <li>Votre mot de passe</li>
  <li>Votre code de vérification reçu par SMS</li>
</ul>

<h4>Règle 4 — Vérifiez avant d'envoyer de l'argent</h4>
<p>Si quelqu'un vous demande de l'argent par message (même un ami) :</p>
<ol>
  <li>Appelez directement la personne pour confirmer</li>
  <li>Ne vous laissez pas presser par l'urgence</li>
  <li>Consultez votre solde avant tout remboursement</li>
</ol>

<h4>Règle 5 — Signalez et prévenez</h4>
<p>Si vous recevez un message suspect :</p>
<ul>
  <li>Signalez-le sur cette plateforme (bouton « Signaler »)</li>
  <li>Prévenez vos contacts dans vos groupes</li>
  <li>Bloquez et signalez le numéro ou profil suspect</li>
</ul>

<h3>Que faire si vous avez été victime ?</h3>

<table>
  <thead>
    <tr>
      <th>Situation</th>
      <th>Actions immédiates</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Vous avez donné votre code PIN Mobile Money</td>
      <td>Appelez immédiatement le service client (MTN : 1555, Orange : 688)</td>
    </tr>
    <tr>
      <td>Votre compte WhatsApp a été piraté</td>
      <td>Contactez support@whatsapp.com et réinstallez l'app</td>
    </tr>
    <tr>
      <td>Vous avez cliqué sur un lien suspect</td>
      <td>Changez tous vos mots de passe et scannez votre téléphone</td>
    </tr>
    <tr>
      <td>Vous avez perdu de l'argent</td>
      <td>Portez plainte à la police et signalez à la PLCC (Plateforme de Lutte contre la Cybercriminalité)</td>
    </tr>
  </tbody>
</table>

<h3>Ressource utile</h3>
<p>En Côte d'Ivoire, la <strong>PLCC (Plateforme de Lutte contre la Cybercriminalité)</strong>
est l'organe officiel pour signaler les cybercriminels. Numéro vert : <strong>1111</strong></p>
HTML
                ,
                'video_url'        => null,
                'order'            => 4,
                'duration_minutes' => 15,
                'is_published'     => true,
                'quiz' => [
                    'title'              => "Quiz 4 — Quiz final : Maîtrise du phishing",
                    'description'        => "Quiz de validation globale du module Phishing. Score minimum : 70%.",
                    'passing_score'      => 70,
                    'time_limit_minutes' => 15,
                    'max_attempts'       => 3,
                    'is_published'       => true,
                    'questions' => [
                        [
                            'text'        => "La vérification en deux étapes (2FA) protège votre compte même si votre mot de passe est volé.",
                            'type'        => 'vrai_faux',
                            'explanation' => "VRAI. Le 2FA exige un second code (envoyé par SMS ou généré par une app) que l'escroc n'a pas, même s'il connaît votre mot de passe.",
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => true],
                                ['text' => 'Faux', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "Quel numéro appeler en Côte d'Ivoire pour signaler une cybercriminalité ?",
                            'type'        => 'qcm',
                            'explanation' => "Le numéro vert de la PLCC (Plateforme de Lutte contre la Cybercriminalité) est le 1111.",
                            'points'      => 2,
                            'answers'     => [
                                ['text' => "1155", 'correct' => false],
                                ['text' => "1111", 'correct' => true],
                                ['text' => "0800", 'correct' => false],
                                ['text' => "0700", 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "Vous recevez un SMS : « Votre compte Wave sera désactivé. Envoyez votre code au 0709… ». Que faites-vous ?",
                            'type'        => 'qcm',
                            'explanation' => "Il faut appeler le service client officiel de Wave (pas le numéro dans le SMS) pour vérifier. Ne jamais envoyer de code à quiconque.",
                            'points'      => 2,
                            'answers'     => [
                                ['text' => "J'envoie mon code au numéro indiqué", 'correct' => false],
                                ['text' => "J'ignore le message", 'correct' => false],
                                ['text' => "J'appelle le service client officiel de Wave pour vérifier", 'correct' => true],
                                ['text' => "J'envoie le SMS à mes contacts pour les prévenir", 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "Lequel de ces comportements vous protège le mieux contre le phishing ?",
                            'type'        => 'qcm',
                            'explanation' => "Utiliser des mots de passe uniques par site + la 2FA sont les deux mesures les plus efficaces combinées.",
                            'points'      => 2,
                            'answers'     => [
                                ['text' => "Utiliser le même mot de passe partout", 'correct' => false],
                                ['text' => "Ne jamais utiliser Internet", 'correct' => false],
                                ['text' => "Activer la 2FA et utiliser des mots de passe uniques", 'correct' => true],
                                ['text' => "Répondre aux emails suspects pour comprendre l'arnaque", 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "Si votre compte WhatsApp est piraté, à qui faut-il écrire en premier ?",
                            'type'        => 'qcm',
                            'explanation' => "L'adresse support@whatsapp.com permet de signaler un compte compromis et d'entamer la procédure de récupération.",
                            'points'      => 1,
                            'answers'     => [
                                ['text' => "À vos amis pour les prévenir", 'correct' => false],
                                ['text' => "Au support WhatsApp (support@whatsapp.com)", 'correct' => true],
                                ['text' => "Au hacker pour récupérer votre compte", 'correct' => false],
                                ['text' => "À la police directement", 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => "Un escroc vous demande 10.000 FCFA via Mobile Money en se faisant passer pour votre employeur. Sans appel de vérification, vous envoyez l'argent.",
                            'type'        => 'vrai_faux',
                            'explanation' => "FAUX. Toujours vérifier en appelant directement, même si l'urgence semble réelle. Les escrocs jouent précisément sur la pression et la confiance en l'autorité.",
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                    ],
                ],
            ],

        ];
    }
}
