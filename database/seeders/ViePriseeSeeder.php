<?php

namespace Database\Seeders;

use App\Models\{Module, Lesson, Quiz, Question, Answer};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * ViePriseeSeeder — ISPA Cyber Academy
 * Module : Vie privée et données personnelles  |  Niveau : universite
 *
 * php artisan db:seed --class=ViePriseeSeeder
 */
class ViePriseeSeeder extends Seeder
{
    public function run(): void
    {
        $module = Module::firstOrCreate(
            ['slug' => 'vie-privee-donnees-personnelles'],
            [
                'title'          => 'Vie privée et données personnelles',
                'description'    => 'Comprenez ce que sont vos données personnelles, comment elles sont '
                                  . 'collectées et exploitées par les applications et réseaux sociaux, '
                                  . 'et apprenez à exercer vos droits numériques en Côte d\'Ivoire.',
                'level'          => 'universite',
                'order'          => 7,
                'duration_hours' => 3,
                'is_published'   => true,
            ]
        );

        $module->lessons()->each(function ($lesson) {
            $lesson->quizzes()->each(function ($quiz) {
                $quiz->questions()->each(fn($q) => $q->answers()->delete());
                $quiz->questions()->delete();
                $quiz->delete();
            });
            $lesson->delete();
        });

        foreach ($this->lessons() as $lessonData) {
            $quizData = $lessonData['quiz'] ?? null;
            unset($lessonData['quiz']);
            $lessonData['module_id'] = $module->id;
            $lesson = Lesson::create($lessonData);
            if ($quizData) $this->createQuiz($lesson, $quizData);
        }

        $this->command->info('✅  [universite] "Vie privée et données personnelles" — ' . $module->lessons()->count() . ' leçons créées.');
    }

    private function createQuiz(Lesson $lesson, array $data): void
    {
        $questions = $data['questions'];
        unset($data['questions']);
        $data['lesson_id'] = $lesson->id;
        $quiz = Quiz::create($data);

        foreach ($questions as $i => $q) {
            $answers = $q['answers'];
            $question = Question::create([
                'quiz_id'       => $quiz->id,
                'question_text' => $q['text'],
                'type'          => $q['type'],
                'explanation'   => $q['explanation'] ?? null,
                'points'        => $q['points'] ?? 1,
                'order'         => $i + 1,
            ]);
            foreach ($answers as $j => $a) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $a['text'],
                    'is_correct'  => $a['correct'],
                    'order'       => $j + 1,
                ]);
            }
        }
    }

    private function lessons(): array
    {
        return [

            // ── Leçon 1 ───────────────────────────────────────────
            [
                'title'            => 'Qu\'est-ce qu\'une donnée personnelle ?',
                'slug'             => 'donnees-personnelles-definition-' . Str::random(4),
                'order'            => 1,
                'duration_minutes' => 15,
                'is_published'     => true,
                'video_url'        => null,
                'content'          => <<<'HTML'
<h2>Qu'est-ce qu'une donnée personnelle ?</h2>

<p>Une <strong>donnée personnelle</strong> est toute information permettant d'identifier
directement ou indirectement une personne physique. Vous en produisez des centaines
chaque jour sans le savoir.</p>

<h3>Exemples de données personnelles</h3>
<table>
  <thead>
    <tr><th>Type</th><th>Exemples</th><th>Sensibilité</th></tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>Identité</strong></td>
      <td>Nom, prénom, date de naissance, photo</td>
      <td>Moyenne</td>
    </tr>
    <tr>
      <td><strong>Contact</strong></td>
      <td>Numéro de téléphone, email, adresse</td>
      <td>Moyenne</td>
    </tr>
    <tr>
      <td><strong>Financières</strong></td>
      <td>Numéro de compte, historique Mobile Money</td>
      <td>Très élevée</td>
    </tr>
    <tr>
      <td><strong>Localisation</strong></td>
      <td>GPS, domicile, lieu de travail</td>
      <td>Élevée</td>
    </tr>
    <tr>
      <td><strong>Comportementales</strong></td>
      <td>Historique de navigation, likes, achats</td>
      <td>Élevée</td>
    </tr>
    <tr>
      <td><strong>Biométriques</strong></td>
      <td>Empreinte digitale, reconnaissance faciale</td>
      <td>Très élevée</td>
    </tr>
  </tbody>
</table>

<h3>Données personnelles et réseaux sociaux</h3>
<p>Chaque jour, en utilisant Facebook, TikTok, WhatsApp ou Instagram, vous partagez :</p>
<ul>
  <li>Votre localisation géographique</li>
  <li>Vos centres d'intérêt et opinions</li>
  <li>Votre réseau de relations</li>
  <li>Vos habitudes de consommation</li>
  <li>Votre visage (reconnaissance faciale)</li>
</ul>

<h3>Le modèle économique des réseaux sociaux</h3>
<p>Si un service numérique est <strong>gratuit</strong>, c'est souvent que <strong>vous êtes
le produit</strong> : vos données sont analysées et vendues à des annonceurs pour
vous cibler avec de la publicité personnalisée.</p>

<blockquote>
  <p>« Vos données personnelles valent plus que de l'or dans l'économie numérique. »</p>
</blockquote>

<h3>Le cadre juridique en Côte d'Ivoire</h3>
<p>La loi ivoirienne <strong>n° 2013-450</strong> relative à la protection des données
à caractère personnel encadre la collecte et l'utilisation de vos données.
L'<strong>ARTCI</strong> (Autorité de Régulation des Télécommunications / TIC)
veille au respect de ces règles.</p>
HTML,
                'quiz' => [
                    'title'              => 'Quiz 1 — Comprendre les données personnelles',
                    'description'        => 'Identifiez ce qui constitue une donnée personnelle.',
                    'passing_score'      => 70,
                    'time_limit_minutes' => 10,
                    'max_attempts'       => 3,
                    'is_published'       => true,
                    'questions' => [
                        [
                            'text'        => 'Lequel de ces éléments N\'est PAS une donnée personnelle ?',
                            'type'        => 'qcm',
                            'explanation' => 'La météo est une information publique qui ne permet pas d\'identifier une personne. Toutes les autres options permettent d\'identifier ou localiser un individu.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Votre numéro de téléphone', 'correct' => false],
                                ['text' => 'La météo d\'Abidjan aujourd\'hui', 'correct' => true],
                                ['text' => 'Votre adresse domicile', 'correct' => false],
                                ['text' => 'Votre empreinte digitale', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Quand un service numérique est gratuit, cela signifie généralement que :',
                            'type'        => 'qcm',
                            'explanation' => 'Le modèle économique de la plupart des services gratuits repose sur la monétisation des données utilisateurs via la publicité ciblée.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'L\'entreprise est généreuse', 'correct' => false],
                                ['text' => 'Vos données personnelles sont le produit vendu', 'correct' => true],
                                ['text' => 'Il n\'y a aucun risque', 'correct' => false],
                                ['text' => 'Le service est financé par l\'État', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Votre historique de navigation sur internet est une donnée personnelle.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'VRAI. L\'historique de navigation révèle vos centres d\'intérêt, vos habitudes, votre état de santé potentiel, etc. C\'est une donnée personnelle comportementale très précieuse.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => true],
                                ['text' => 'Faux', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Quelle institution ivoirienne régule la protection des données personnelles ?',
                            'type'        => 'qcm',
                            'explanation' => 'L\'ARTCI (Autorité de Régulation des Télécommunications/TIC de Côte d\'Ivoire) veille à la protection des données personnelles.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'PLCC', 'correct' => false],
                                ['text' => 'ARTCI', 'correct' => true],
                                ['text' => 'MESRS', 'correct' => false],
                                ['text' => 'SNEDAI', 'correct' => false],
                            ],
                        ],
                    ],
                ],
            ],

            // ── Leçon 2 ───────────────────────────────────────────
            [
                'title'            => 'Comment les applications collectent et utilisent vos données',
                'slug'             => 'collecte-donnees-applications-' . Str::random(4),
                'order'            => 2,
                'duration_minutes' => 18,
                'is_published'     => true,
                'video_url'        => null,
                'content'          => <<<'HTML'
<h2>Comment les applications collectent et utilisent vos données</h2>

<p>Chaque application installée sur votre téléphone peut collecter des données vous concernant.
Comprendre ces mécanismes vous permet de faire des choix éclairés.</p>

<h3>Les permissions : la porte d'entrée vers vos données</h3>
<p>Quand vous installez une application, elle demande des autorisations :</p>
<table>
  <thead><tr><th>Permission</th><th>Données accessibles</th><th>Risque si accordé à tort</th></tr></thead>
  <tbody>
    <tr><td>Localisation</td><td>Position GPS en temps réel</td><td>Surveillance de vos déplacements</td></tr>
    <tr><td>Contacts</td><td>Tous vos numéros et emails</td><td>Revente à des spammeurs</td></tr>
    <tr><td>Caméra / Micro</td><td>Photos, vidéos, conversations</td><td>Espionnage</td></tr>
    <tr><td>Stockage</td><td>Photos, documents, fichiers</td><td>Accès à vos documents personnels</td></tr>
    <tr><td>SMS</td><td>Lecture de vos messages</td><td>Vol de codes 2FA</td></tr>
  </tbody>
</table>

<h3>Règle d'or des permissions</h3>
<p>Accordez uniquement les permissions <strong>nécessaires au fonctionnement</strong> de l'application :</p>
<ul>
  <li>Une lampe de poche n'a pas besoin d'accéder à vos contacts</li>
  <li>Une application de calcul n'a pas besoin de votre localisation</li>
  <li>Un jeu n'a pas besoin d'accéder à vos SMS</li>
</ul>
<p>Si une application demande des permissions non justifiées → <strong>refusez ou désinstallez.</strong></p>

<h3>Les cookies et le pistage web</h3>
<p>Les <strong>cookies</strong> sont de petits fichiers stockés par les sites web pour :</p>
<ul>
  <li>Mémoriser vos préférences (langue, panier d'achat)</li>
  <li>Vous garder connecté</li>
  <li>Suivre votre navigation pour de la publicité ciblée</li>
</ul>
<p>Lorsqu'un site demande votre consentement aux cookies, vous pouvez
<strong>refuser les cookies non essentiels</strong> sans perdre l'accès au site.</p>

<h3>Bonnes pratiques pour protéger vos données</h3>
<ul>
  <li>Vérifiez les permissions de chaque application dans les paramètres de votre téléphone</li>
  <li>Désinstallez les applications que vous n'utilisez plus</li>
  <li>Utilisez un navigateur respectueux de la vie privée (Firefox, Brave)</li>
  <li>Activez la navigation privée pour les recherches sensibles</li>
  <li>Lisez les politiques de confidentialité (au moins la partie sur les données collectées)</li>
</ul>

<h3>Vos droits sur vos données</h3>
<p>Selon la loi ivoirienne n° 2013-450, vous avez le droit :</p>
<ul>
  <li><strong>D'accès</strong> : savoir quelles données sont collectées sur vous</li>
  <li><strong>De rectification</strong> : corriger des données erronées</li>
  <li><strong>D'opposition</strong> : refuser certains traitements</li>
  <li><strong>De suppression</strong> : demander la suppression de vos données</li>
</ul>
HTML,
                'quiz' => [
                    'title'              => 'Quiz 2 — Permissions et collecte de données',
                    'description'        => 'Évaluez votre compréhension des mécanismes de collecte.',
                    'passing_score'      => 70,
                    'time_limit_minutes' => 12,
                    'max_attempts'       => 3,
                    'is_published'       => true,
                    'questions' => [
                        [
                            'text'        => 'Une application de lampe de poche demande accès à vos contacts. Que faites-vous ?',
                            'type'        => 'qcm',
                            'explanation' => 'Une lampe de poche n\'a aucune raison d\'accéder à vos contacts. Cette permission non justifiée est un signal d\'alerte — refusez ou désinstallez.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'J\'accepte pour ne pas perdre la fonctionnalité', 'correct' => false],
                                ['text' => 'Je refuse cette permission et désinstalle si insistant', 'correct' => true],
                                ['text' => 'J\'accepte car c\'est une application populaire', 'correct' => false],
                                ['text' => 'Je ne fais pas attention aux permissions', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Quelle permission est la plus dangereuse à accorder à une application non fiable ?',
                            'type'        => 'qcm',
                            'explanation' => 'L\'accès aux SMS permet de lire les codes de double authentification (2FA), donnant accès à tous vos comptes protégés par cette méthode.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Accès à la lampe de poche', 'correct' => false],
                                ['text' => 'Accès aux SMS', 'correct' => true],
                                ['text' => 'Accès au thème de l\'application', 'correct' => false],
                                ['text' => 'Accès au volume sonore', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Refuser les cookies non essentiels sur un site web vous empêche d\'y accéder.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'FAUX. Refuser les cookies non essentiels (publicitaires, de tracking) ne vous empêche pas d\'utiliser le site. Seuls les cookies techniquement nécessaires au fonctionnement sont obligatoires.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                        [
                            'text'        => 'Selon la loi ivoirienne, vous avez le droit de demander la suppression de vos données personnelles.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'VRAI. La loi n° 2013-450 vous confère le droit à la suppression de vos données (droit à l\'oubli).',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => true],
                                ['text' => 'Faux', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Quelle bonne pratique protège le mieux votre vie privée en ligne ?',
                            'type'        => 'qcm',
                            'explanation' => 'Vérifier et limiter les permissions des applications, et désinstaller celles inutilisées, réduit considérablement la surface de collecte de données.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Accepter toutes les permissions pour éviter les messages d\'erreur', 'correct' => false],
                                ['text' => 'Vérifier et limiter les permissions + désinstaller les apps inutilisées', 'correct' => true],
                                ['text' => 'Utiliser uniquement des applications payantes', 'correct' => false],
                                ['text' => 'Ne jamais utiliser internet sur son téléphone', 'correct' => false],
                            ],
                        ],
                    ],
                ],
            ],

            // ── Leçon 3 ───────────────────────────────────────────
            [
                'title'            => 'Cyberharcèlement et atteinte à la vie privée',
                'slug'             => 'cyberharcelement-vie-privee-' . Str::random(4),
                'order'            => 3,
                'duration_minutes' => 18,
                'is_published'     => true,
                'video_url'        => null,
                'content'          => <<<'HTML'
<h2>Cyberharcèlement et atteinte à la vie privée</h2>

<p>Le <strong>cyberharcèlement</strong> est l'utilisation des technologies numériques
pour harceler, menacer, humilier ou intimider une personne. C'est un phénomène en
forte croissance en Côte d'Ivoire, particulièrement chez les jeunes.</p>

<h3>Les formes de cyberharcèlement</h3>
<ul>
  <li><strong>Diffusion de contenus intimes sans consentement</strong> (revenge porn) — punie par la loi</li>
  <li><strong>Harcèlement répété</strong> par messages, commentaires, appels</li>
  <li><strong>Usurpation d'identité</strong> pour nuire à la réputation</li>
  <li><strong>Chantage numérique</strong> : menace de diffuser des photos ou informations</li>
  <li><strong>Doxing</strong> : publication publique d'informations privées (adresse, lieu de travail)</li>
</ul>

<h3>Cadre légal en Côte d'Ivoire</h3>
<p>La loi ivoirienne sur la cybercriminalité (<strong>loi n° 2013-451</strong>) punit :</p>
<ul>
  <li>La diffusion non consentie d'images intimes</li>
  <li>L'usurpation d'identité numérique</li>
  <li>Le chantage et l'extorsion via les réseaux</li>
  <li>L'accès non autorisé à des données personnelles</li>
</ul>
<p>Les peines peuvent aller jusqu'à <strong>plusieurs années d'emprisonnement</strong>
et de lourdes amendes.</p>

<h3>Comment se protéger du cyberharcèlement</h3>
<ul>
  <li>Configurez vos comptes en <strong>mode privé</strong></li>
  <li>Ne partagez <strong>jamais de photos intimes</strong> numériquement</li>
  <li>Utilisez la <strong>double authentification</strong> pour protéger vos comptes</li>
  <li>Vérifiez régulièrement les applications ayant accès à votre compte</li>
</ul>

<h3>Que faire si vous êtes victime ?</h3>
<ol>
  <li><strong>Ne répondez pas</strong> à l'harceleur</li>
  <li><strong>Bloquez</strong> l'auteur sur toutes les plateformes</li>
  <li><strong>Faites des captures d'écran</strong> de toutes les preuves</li>
  <li><strong>Signalez</strong> le contenu aux plateformes (Facebook, Instagram, TikTok)</li>
  <li><strong>Portez plainte</strong> à la PLCC au <strong>1111</strong></li>
  <li><strong>Parlez-en</strong> à un adulte de confiance ou un professionnel</li>
</ol>

<h3>Ressources d'aide</h3>
<ul>
  <li>PLCC — Cybercriminalité : <strong>1111</strong></li>
  <li>Police Nationale : <strong>111</strong></li>
  <li>Signalement ISPA : bouton <strong>« Signaler »</strong> de cette plateforme</li>
</ul>
HTML,
                'quiz' => [
                    'title'              => 'Quiz Final — Vie privée et cyberharcèlement',
                    'description'        => 'Quiz de validation du module. Score minimum : 70%.',
                    'passing_score'      => 70,
                    'time_limit_minutes' => 15,
                    'max_attempts'       => 3,
                    'is_published'       => true,
                    'questions' => [
                        [
                            'text'        => 'La diffusion de photos intimes sans consentement est punie par la loi en Côte d\'Ivoire.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'VRAI. La loi n° 2013-451 sur la cybercriminalité punit sévèrement la diffusion non consentie d\'images intimes.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => true],
                                ['text' => 'Faux', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Quelqu\'un menace de publier vos photos si vous ne lui envoyez pas de l\'argent. Quelle est la bonne réaction ?',
                            'type'        => 'qcm',
                            'explanation' => 'Ne jamais payer un chantage numérique — cela encourage l\'escroc à continuer. Il faut collecter les preuves et porter plainte à la PLCC.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Je paye pour qu\'il arrête', 'correct' => false],
                                ['text' => 'Je collecte les preuves et porte plainte à la PLCC (1111)', 'correct' => true],
                                ['text' => 'Je supprime mes réseaux sociaux', 'correct' => false],
                                ['text' => 'Je négocie avec lui', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Le doxing consiste à :',
                            'type'        => 'qcm',
                            'explanation' => 'Le doxing est la publication publique non consentie d\'informations personnelles privées (adresse, lieu de travail, numéro de téléphone) pour nuire à quelqu\'un.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Créer de faux documents officiels', 'correct' => false],
                                ['text' => 'Publier publiquement des informations privées d\'une personne pour lui nuire', 'correct' => true],
                                ['text' => 'Pirater un compte de réseau social', 'correct' => false],
                                ['text' => 'Envoyer des virus par email', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Pour se protéger du cyberharcèlement, il suffit de changer de numéro de téléphone.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'FAUX. Le cyberharcèlement se déroule principalement via les réseaux sociaux et plateformes en ligne. La protection passe par la configuration des paramètres de confidentialité, le blocage et le signalement.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                        [
                            'text'        => 'Quelle est la première chose à faire avant de porter plainte pour cyberharcèlement ?',
                            'type'        => 'qcm',
                            'explanation' => 'Les captures d\'écran sont les preuves essentielles pour une plainte. Sans preuves, il est difficile d\'identifier et de poursuivre l\'auteur.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Supprimer mon compte pour protéger ma vie privée', 'correct' => false],
                                ['text' => 'Faire des captures d\'écran de toutes les preuves', 'correct' => true],
                                ['text' => 'Répondre à l\'harceleur pour qu\'il explique ses raisons', 'correct' => false],
                                ['text' => 'Publier un post pour informer mes amis', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Partager numériquement des photos intimes avec un(e) partenaire de confiance ne présente aucun risque.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'FAUX. Une fois une image partagée numériquement, vous perdez tout contrôle sur elle. Les relations peuvent évoluer et les appareils peuvent être piratés ou volés.',
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
