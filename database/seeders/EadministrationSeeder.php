<?php

namespace Database\Seeders;

use App\Models\{Module, Lesson, Quiz, Question, Answer};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * EadministrationSeeder — ISPA Cyber Academy
 * Module : E-administration  |  Niveau : universite
 *
 * php artisan db:seed --class=EadministrationSeeder
 */
class EadministrationSeeder extends Seeder
{
    public function run(): void
    {
        $module = Module::firstOrCreate(
            ['slug' => 'e-administration-securisee'],
            [
                'title'          => 'E-administration et démarches en ligne',
                'description'    => 'Apprenez à effectuer vos démarches administratives en ligne en toute sécurité : '
                                  . 'déclarations, demandes de documents officiels, portails gouvernementaux ivoiriens '
                                  . 'et protection contre les faux sites administratifs.',
                'level'          => 'universite',
                'order'          => 6,
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

        $this->command->info('✅  [universite] "E-administration" — ' . $module->lessons()->count() . ' leçons créées.');
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
                'title'            => 'Les services administratifs numériques en Côte d\'Ivoire',
                'slug'             => 'services-numeriques-ci-' . Str::random(4),
                'order'            => 1,
                'duration_minutes' => 15,
                'is_published'     => true,
                'video_url'        => null,
                'content'          => <<<'HTML'
<h2>L'e-administration en Côte d'Ivoire</h2>

<p>La <strong>transformation numérique</strong> de l'administration ivoirienne permet aujourd'hui
d'effectuer de nombreuses démarches sans se déplacer. Ces services offrent gain de temps,
mais nécessitent des précautions de sécurité spécifiques.</p>

<h3>Les principaux portails officiels</h3>
<table>
  <thead>
    <tr><th>Service</th><th>Portail officiel</th><th>Démarches disponibles</th></tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>État civil</strong></td>
      <td>service-public.ci</td>
      <td>Actes de naissance, mariage, casier judiciaire</td>
    </tr>
    <tr>
      <td><strong>Impôts</strong></td>
      <td>dgi.gouv.ci</td>
      <td>Déclarations fiscales, attestations</td>
    </tr>
    <tr>
      <td><strong>Douanes</strong></td>
      <td>douanes.gouv.ci</td>
      <td>Dédouanement, déclarations</td>
    </tr>
    <tr>
      <td><strong>Passeport / CNI</strong></td>
      <td>snedai.ci</td>
      <td>Demande et renouvellement de documents</td>
    </tr>
    <tr>
      <td><strong>Universités</strong></td>
      <td>mesrs.gouv.ci</td>
      <td>Inscriptions, bourses, résultats</td>
    </tr>
  </tbody>
</table>

<h3>Comment identifier un site gouvernemental officiel ?</h3>
<ul>
  <li>✅ L'URL se termine par <strong>.gouv.ci</strong> ou <strong>.ci</strong></li>
  <li>✅ Le cadenas HTTPS est présent dans la barre d'adresse</li>
  <li>✅ Le design est sobre et institutionnel</li>
  <li>❌ Méfiance si : fautes d'orthographe, demande de paiement immédiat en Mobile Money</li>
</ul>

<h3>Les risques de l'e-administration</h3>
<ul>
  <li><strong>Faux sites administratifs</strong> imitant les portails officiels</li>
  <li><strong>Intermédiaires non officiels</strong> proposant des services payants inutiles</li>
  <li><strong>Hameçonnage</strong> via de faux emails gouvernementaux</li>
  <li><strong>Vol d'identité</strong> par capture de documents personnels</li>
</ul>
HTML,
                'quiz' => [
                    'title'              => 'Quiz 1 — Connaître les services numériques ivoiriens',
                    'description'        => 'Vérifiez votre connaissance des portails officiels.',
                    'passing_score'      => 70,
                    'time_limit_minutes' => 10,
                    'max_attempts'       => 3,
                    'is_published'       => true,
                    'questions' => [
                        [
                            'text'        => 'Quel domaine identifie un site gouvernemental officiel ivoirien ?',
                            'type'        => 'qcm',
                            'explanation' => 'Le domaine .gouv.ci est réservé aux institutions gouvernementales ivoiriennes.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => '.gouv.ci', 'correct' => true],
                                ['text' => '.gov.com', 'correct' => false],
                                ['text' => '.ci-officiel.net', 'correct' => false],
                                ['text' => '.administration-ci.ml', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Sur quel portail s\'inscrit-on pour les demandes de passeport en CI ?',
                            'type'        => 'qcm',
                            'explanation' => 'La SNEDAI (snedai.ci) gère les documents d\'identité en Côte d\'Ivoire.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'dgi.gouv.ci', 'correct' => false],
                                ['text' => 'snedai.ci', 'correct' => true],
                                ['text' => 'mesrs.gouv.ci', 'correct' => false],
                                ['text' => 'douanes.gouv.ci', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Un site demandant un paiement Mobile Money pour accélérer une démarche officielle est fiable.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'FAUX. Les sites gouvernementaux officiels n\'utilisent pas le Mobile Money comme seul moyen de paiement pour leurs services.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                        [
                            'text'        => 'La présence du cadenas HTTPS garantit qu\'un site est officiel et sécurisé.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'FAUX. HTTPS signifie que la connexion est chiffrée, mais un site frauduleux peut aussi utiliser HTTPS. Vérifiez toujours le nom de domaine complet.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                    ],
                ],
            ],

            // ── Leçon 2 ───────────────────────────────────────────
            [
                'title'            => 'Sécuriser ses démarches en ligne',
                'slug'             => 'securiser-demarches-en-ligne-' . Str::random(4),
                'order'            => 2,
                'duration_minutes' => 18,
                'is_published'     => true,
                'video_url'        => null,
                'content'          => <<<'HTML'
<h2>Comment effectuer ses démarches administratives en toute sécurité</h2>

<h3>Étape 1 — Vérifier l'URL avant tout</h3>
<p>Avant de saisir la moindre information personnelle, vérifiez l'adresse du site :</p>
<ul>
  <li>✅ <code>https://www.service-public.ci</code> — Officiel</li>
  <li>❌ <code>http://service-public-ci.com</code> — Frauduleux</li>
  <li>❌ <code>https://ci-service-public.net/acte-naissance</code> — Frauduleux</li>
</ul>
<p>⚠️ Tapez toujours l'adresse vous-même dans le navigateur. Ne passez jamais par un lien reçu par SMS ou email.</p>

<h3>Étape 2 — Protéger vos documents personnels</h3>
<p>Lors d'une démarche en ligne, vous devrez parfois téléverser des documents :</p>
<ul>
  <li>CNI, passeport, acte de naissance</li>
  <li>Justificatifs de domicile</li>
  <li>Diplômes et relevés de notes</li>
</ul>
<p><strong>Règles de protection :</strong></p>
<ul>
  <li>Ne téléversez des documents que sur des portails officiels (.gouv.ci)</li>
  <li>Ne transmettez jamais de copies par WhatsApp ou email à des inconnus</li>
  <li>Vérifiez les mentions légales et la politique de confidentialité du site</li>
</ul>

<h3>Étape 3 — Gérer vos identifiants de connexion</h3>
<ul>
  <li>Créez un mot de passe fort et unique pour chaque portail</li>
  <li>Activez la double authentification si disponible</li>
  <li>Ne partagez jamais vos identifiants avec des intermédiaires</li>
  <li>Déconnectez-vous après chaque session, surtout en cybercafé</li>
</ul>

<h3>Étape 4 — Reconnaître les arnaques aux démarches administratives</h3>
<table>
  <thead><tr><th>Signal d'alerte</th><th>Signification</th></tr></thead>
  <tbody>
    <tr><td>Site non officiel (.com, .net, .ml)</td><td>Faux site imitant l'administration</td></tr>
    <tr><td>Promesse de traitement accéléré contre paiement</td><td>Arnaque aux faux intermédiaires</td></tr>
    <tr><td>Demande de copie de CNI par WhatsApp</td><td>Vol d'identité potentiel</td></tr>
    <tr><td>Email avec lien vers un formulaire urgent</td><td>Phishing administratif</td></tr>
  </tbody>
</table>

<h3>Les intermédiaires non officiels</h3>
<p>De nombreuses personnes proposent sur les réseaux sociaux d'effectuer des démarches
administratives à votre place, moyennant paiement. Ces pratiques sont à la fois
<strong>risquées</strong> (vol d'identité) et parfois <strong>illégales</strong>.</p>
<p>✅ Effectuez toujours vos démarches vous-même ou via un agent administratif officiel.</p>
HTML,
                'quiz' => [
                    'title'              => 'Quiz 2 — Sécuriser ses démarches administratives',
                    'description'        => 'Testez votre capacité à effectuer des démarches en ligne en sécurité.',
                    'passing_score'      => 70,
                    'time_limit_minutes' => 12,
                    'max_attempts'       => 3,
                    'is_published'       => true,
                    'questions' => [
                        [
                            'text'        => 'Quelle est la meilleure façon d\'accéder à un portail administratif officiel ?',
                            'type'        => 'qcm',
                            'explanation' => 'Taper l\'URL directement dans le navigateur évite de tomber sur un faux site via un lien frauduleux.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Cliquer sur un lien reçu par SMS', 'correct' => false],
                                ['text' => 'Taper l\'URL officielle directement dans le navigateur', 'correct' => true],
                                ['text' => 'Passer par un intermédiaire sur Facebook', 'correct' => false],
                                ['text' => 'Utiliser le premier résultat Google', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Il est acceptable d\'envoyer une copie de sa CNI par WhatsApp à un intermédiaire qui propose d\'accélérer une démarche.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'FAUX. Envoyer des documents d\'identité par WhatsApp expose au vol d\'identité. Les démarches officielles se font uniquement sur les portails gouvernementaux.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                        [
                            'text'        => 'En cybercafé, après avoir effectué une démarche administrative en ligne, vous devez :',
                            'type'        => 'qcm',
                            'explanation' => 'Se déconnecter et effacer l\'historique empêche un autre utilisateur d\'accéder à votre session et à vos données.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Laisser la session ouverte pour la prochaine fois', 'correct' => false],
                                ['text' => 'Vous déconnecter et effacer l\'historique du navigateur', 'correct' => true],
                                ['text' => 'Sauvegarder vos documents sur l\'ordinateur du cybercafé', 'correct' => false],
                                ['text' => 'Rien de spécial', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Un site en .gouv.ci vous garantit automatiquement que vos données sont protégées sans aucune autre vérification.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'FAUX. Même sur un site officiel, il faut vérifier la politique de confidentialité, utiliser un mot de passe fort et se déconnecter après chaque session.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                        [
                            'text'        => 'Parmi ces pratiques, laquelle est la plus sûre pour gérer ses identifiants administratifs ?',
                            'type'        => 'qcm',
                            'explanation' => 'Un mot de passe unique par portail + la double authentification constituent la meilleure protection.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Utiliser le même mot de passe pour tous les portails', 'correct' => false],
                                ['text' => 'Partager ses identifiants avec un ami de confiance', 'correct' => false],
                                ['text' => 'Créer un mot de passe unique par portail et activer la 2FA', 'correct' => true],
                                ['text' => 'Écrire ses mots de passe dans un SMS à soi-même', 'correct' => false],
                            ],
                        ],
                    ],
                ],
            ],

            // ── Leçon 3 ───────────────────────────────────────────
            [
                'title'            => 'Cas pratiques : démarches universitaires en ligne',
                'slug'             => 'demarches-universitaires-ci-' . Str::random(4),
                'order'            => 3,
                'duration_minutes' => 15,
                'is_published'     => true,
                'video_url'        => null,
                'content'          => <<<'HTML'
<h2>Démarches universitaires numériques en CI : cas pratiques</h2>

<p>En tant qu'étudiant, vous serez amené à effectuer plusieurs démarches en ligne
via les portails des universités et du ministère. Voici les situations les plus courantes
et comment les gérer en sécurité.</p>

<h3>Cas 1 — Inscription en ligne à l'université</h3>
<p><strong>Portail officiel :</strong> mesrs.gouv.ci ou le portail de votre université</p>
<p><strong>Documents requis :</strong> Relevé de notes, baccalauréat, CNI, photo d'identité</p>
<p><strong>Pièges à éviter :</strong></p>
<ul>
  <li>Des groupes WhatsApp proposent des « places garanties » contre paiement → Arnaque</li>
  <li>Des sites imitant le portail MESRS → Vérifiez toujours l'URL exacte</li>
  <li>Des emails prétendant venir de l'université → Vérifiez via le site officiel</li>
</ul>

<h3>Cas 2 — Demande de bourse en ligne</h3>
<p><strong>Portail officiel :</strong> gouv.ci ou le portail de la DGES</p>
<p><strong>Attention aux arnaques :</strong> Des individus promettent d'obtenir une bourse
moyennant paiement. Ces pratiques sont frauduleuses — les bourses officielles
ne nécessitent jamais d'intermédiaire payant.</p>

<h3>Cas 3 — Consultation des résultats en ligne</h3>
<p>Vérifiez que vous êtes sur le bon portail avant de saisir votre numéro étudiant :</p>
<ul>
  <li>✅ Portails officiels des universités ivoiriennes (UAO, UFHB, INPHB…)</li>
  <li>❌ Sites tiers non officiels collectant vos identifiants</li>
</ul>

<h3>Que faire en cas d'arnaque à la démarche administrative ?</h3>
<ol>
  <li>Ne payez plus rien à l'escroc</li>
  <li>Conservez toutes les preuves (captures d'écran, messages)</li>
  <li>Signalez à la PLCC au <strong>1111</strong></li>
  <li>Déposez une plainte à la police</li>
  <li>Contactez directement l'institution concernée</li>
</ol>

<h3>Ressources officielles</h3>
<ul>
  <li><strong>MESRS :</strong> mesrs.gouv.ci — Ministère de l'Enseignement Supérieur</li>
  <li><strong>SNEDAI :</strong> snedai.ci — Documents d'identité</li>
  <li><strong>PLCC :</strong> 1111 — Cybercriminalité</li>
  <li><strong>ARTCI :</strong> artci.ci — Régulation des TIC</li>
</ul>
HTML,
                'quiz' => [
                    'title'              => 'Quiz Final — E-administration universitaire',
                    'description'        => 'Quiz de validation du module. Score minimum : 70%.',
                    'passing_score'      => 70,
                    'time_limit_minutes' => 15,
                    'max_attempts'       => 3,
                    'is_published'       => true,
                    'questions' => [
                        [
                            'text'        => 'Un groupe WhatsApp propose des places garanties à l\'université contre 50.000 FCFA. Que faites-vous ?',
                            'type'        => 'qcm',
                            'explanation' => 'Les inscriptions universitaires officielles se font uniquement sur les portails gouvernementaux. Tout intermédiaire payant est une arnaque.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Je paye car c\'est une opportunité', 'correct' => false],
                                ['text' => 'Je refuse et signale l\'arnaque à la PLCC', 'correct' => true],
                                ['text' => 'Je négocie le prix', 'correct' => false],
                                ['text' => 'Je partage dans d\'autres groupes', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Quel organisme gère les documents d\'identité (CNI, passeport) en Côte d\'Ivoire ?',
                            'type'        => 'qcm',
                            'explanation' => 'La SNEDAI (snedai.ci) est l\'organisme officiel pour les documents d\'identité en CI.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'MESRS', 'correct' => false],
                                ['text' => 'SNEDAI', 'correct' => true],
                                ['text' => 'ARTCI', 'correct' => false],
                                ['text' => 'PLCC', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Pour obtenir une bourse officielle, il est normal de payer un intermédiaire.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'FAUX. Les bourses officielles ne nécessitent aucun intermédiaire payant. Toute demande de paiement pour obtenir une bourse est une arnaque.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                        [
                            'text'        => 'En cas d\'arnaque à la démarche administrative, quelle est la première chose à faire ?',
                            'type'        => 'qcm',
                            'explanation' => 'Conserver les preuves (captures d\'écran, messages) est essentiel pour porter plainte efficacement.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Supprimer toutes les conversations', 'correct' => false],
                                ['text' => 'Payer encore pour récupérer son argent', 'correct' => false],
                                ['text' => 'Conserver les preuves et signaler à la PLCC', 'correct' => true],
                                ['text' => 'Ne rien faire et accepter la perte', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Quel est le portail officiel du Ministère de l\'Enseignement Supérieur ivoirien ?',
                            'type'        => 'qcm',
                            'explanation' => 'Le MESRS est accessible sur mesrs.gouv.ci.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'education-ci.com', 'correct' => false],
                                ['text' => 'mesrs.gouv.ci', 'correct' => true],
                                ['text' => 'universite-ci.net', 'correct' => false],
                                ['text' => 'bourse-ci.ml', 'correct' => false],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
