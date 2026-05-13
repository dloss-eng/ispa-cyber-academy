<?php

namespace Database\Seeders;

use App\Models\{Module, Lesson, Quiz, Question, Answer};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * MobileMoneySeeder — ISPA Cyber Academy
 * Module : Sécurité Mobile Money  |  Niveau : lycee
 *
 * php artisan db:seed --class=MobileMoneySeeder
 */
class MobileMoneySeeder extends Seeder
{
    public function run(): void
    {
        $module = Module::firstOrCreate(
            ['slug' => 'securite-mobile-money'],
            [
                'title'          => 'Sécurité Mobile Money',
                'description'    => 'Protège ton argent sur MTN MoMo, Orange Money et Wave. '
                                  . 'Apprends à reconnaître les arnaques les plus courantes '
                                  . 'en Côte d\'Ivoire et à sécuriser tes transactions.',
                'level'          => 'lycee',
                'order'          => 5,
                'duration_hours' => 2,
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

        $this->command->info('✅  [lycee] "Sécurité Mobile Money" — ' . $module->lessons()->count() . ' leçons créées.');
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
                'title'            => 'Comment fonctionne le Mobile Money ?',
                'slug'             => 'fonctionnement-mobile-money-' . Str::random(4),
                'order'            => 1,
                'duration_minutes' => 12,
                'is_published'     => true,
                'video_url'        => null,
                'content'          => <<<'HTML'
<h2>Le Mobile Money en Côte d'Ivoire</h2>

<p>Le <strong>Mobile Money</strong> permet d'envoyer et de recevoir de l'argent,
de payer des factures et d'acheter des biens directement depuis ton téléphone,
sans avoir besoin d'un compte bancaire traditionnel.</p>

<h3>Les 3 grands opérateurs en CI</h3>
<table>
  <thead>
    <tr><th>Opérateur</th><th>Service</th><th>Code USSD</th><th>Service client</th></tr>
  </thead>
  <tbody>
    <tr><td><strong>MTN</strong></td><td>MoMo</td><td>#155#</td><td>1555</td></tr>
    <tr><td><strong>Orange</strong></td><td>Orange Money</td><td>#144#</td><td>688</td></tr>
    <tr><td><strong>Wave</strong></td><td>Wave CI</td><td>Application mobile</td><td>In-app</td></tr>
  </tbody>
</table>

<h3>Comment ça fonctionne ?</h3>
<ol>
  <li>Tu t'inscris avec ton numéro de téléphone et une pièce d'identité</li>
  <li>Tu crées un <strong>code PIN secret</strong> (4 à 6 chiffres)</li>
  <li>Tu déposes de l'argent chez un agent agréé</li>
  <li>Tu effectues tes transactions via le code USSD ou l'application</li>
</ol>

<h3>⚠️ La règle absolue du Mobile Money</h3>
<p style="border-left:4px solid #ff6b35;padding:16px;border-radius:4px;font-size:15px">
  <strong>Ton code PIN est STRICTEMENT PERSONNEL.</strong><br>
  Ne le communique JAMAIS à personne — ni à un agent, ni à un ami,
  ni à quelqu'un qui prétend travailler pour MTN, Orange ou Wave.
</p>
HTML,
                'quiz' => [
                    'title'              => 'Quiz 1 — Fonctionnement du Mobile Money',
                    'description'        => 'Vérifie ta compréhension des bases.',
                    'passing_score'      => 70,
                    'time_limit_minutes' => 10,
                    'max_attempts'       => 3,
                    'is_published'       => true,
                    'questions' => [
                        [
                            'text'        => 'Quel est le code USSD de MTN MoMo en Côte d\'Ivoire ?',
                            'type'        => 'qcm',
                            'explanation' => 'Le code USSD de MTN MoMo est #155#.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => '#144#', 'correct' => false],
                                ['text' => '#155#', 'correct' => true],
                                ['text' => '#100#', 'correct' => false],
                                ['text' => '#200#', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Tu peux partager ton code PIN Mobile Money avec un agent agréé.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'FAUX. Le code PIN est strictement personnel et ne doit jamais être partagé, même avec un agent.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                        [
                            'text'        => 'Que faut-il présenter pour s\'inscrire au Mobile Money ?',
                            'type'        => 'qcm',
                            'explanation' => 'L\'inscription au Mobile Money requiert ton numéro de téléphone et une pièce d\'identité valide.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Ton numéro de téléphone et une pièce d\'identité', 'correct' => true],
                                ['text' => 'Seulement ton nom', 'correct' => false],
                                ['text' => 'Un compte bancaire', 'correct' => false],
                                ['text' => 'Une autorisation parentale', 'correct' => false],
                            ],
                        ],
                    ],
                ],
            ],

            // ── Leçon 2 ───────────────────────────────────────────
            [
                'title'            => 'Les arnaques Mobile Money les plus courantes',
                'slug'             => 'arnaques-mobile-money-' . Str::random(4),
                'order'            => 2,
                'duration_minutes' => 15,
                'is_published'     => true,
                'video_url'        => null,
                'content'          => <<<'HTML'
<h2>Les arnaques Mobile Money les plus courantes en CI</h2>

<h3>Arnaque 1 — Le faux transfert par erreur</h3>
<p>L'escroc t'appelle en disant : <em>« Mon frère, j'ai fait un transfert par erreur sur ton numéro.
Peux-tu me renvoyer les 20.000 FCFA ? »</em></p>
<p>En réalité, <strong>aucun transfert n'a été effectué</strong>. Il attend que tu envoies
l'argent de ta propre poche.</p>
<p>✅ <strong>Réflexe :</strong> Vérifier ton solde AVANT de renvoyer quoi que ce soit.</p>

<h3>Arnaque 2 — Le SMS de gain</h3>
<p style="border-left:4px solid #ff6b35;padding:12px;font-family:monospace">
  📱 « Félicitations ! Vous avez été sélectionné pour recevoir 500.000 FCFA de MTN.
  Appelez le 0700112233 pour récupérer votre gain. »
</p>
<p>C'est <strong>toujours une arnaque</strong>. MTN, Orange et Wave n'organisent pas
de loteries par SMS non sollicités.</p>

<h3>Arnaque 3 — Le faux agent</h3>
<p>Un individu se présente comme agent MTN/Orange et te demande ton code PIN
pour « vérifier ton compte » ou « débloquer une prime ».</p>
<p>✅ <strong>Réflexe :</strong> Un vrai agent n'a jamais besoin de ton code PIN.</p>

<h3>Arnaque 4 — Le faux remboursement</h3>
<p>Un inconnu te dit qu'une entreprise te doit un remboursement mais qu'il faut d'abord
payer des « frais d'activation » de quelques milliers de FCFA.</p>
<p>✅ <strong>Réflexe :</strong> Aucun remboursement légitime ne demande un paiement préalable.</p>

<h3>Comment signaler une arnaque ?</h3>
<ul>
  <li>MTN : appel au <strong>1555</strong></li>
  <li>Orange : appel au <strong>688</strong></li>
  <li>PLCC (Police cybercriminalité) : <strong>1111</strong></li>
  <li>Cette plateforme : bouton <strong>« Signaler »</strong></li>
</ul>
HTML,
                'quiz' => [
                    'title'              => 'Quiz 2 — Identifier les arnaques Mobile Money',
                    'description'        => 'Sauras-tu reconnaître les pièges ?',
                    'passing_score'      => 70,
                    'time_limit_minutes' => 10,
                    'max_attempts'       => 3,
                    'is_published'       => true,
                    'questions' => [
                        [
                            'text'        => 'Quelqu\'un t\'appelle pour dire qu\'il a fait un transfert par erreur et te demande de renvoyer l\'argent. Que fais-tu ?',
                            'type'        => 'qcm',
                            'explanation' => 'Vérifie toujours ton solde avant de renvoyer de l\'argent. Si aucun transfert n\'apparaît, c\'est une arnaque.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Je renvoie immédiatement pour rendre service', 'correct' => false],
                                ['text' => 'Je vérifie mon solde et contacte le service client si nécessaire', 'correct' => true],
                                ['text' => 'Je lui donne mon code PIN pour qu\'il reprenne lui-même', 'correct' => false],
                                ['text' => 'Je transfère le double pour être généreux', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Un SMS t\'annonce que tu as gagné 1.000.000 FCFA de MTN. C\'est forcément vrai.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'FAUX. MTN ne distribue pas d\'argent via des SMS non sollicités. C\'est une arnaque classique.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                        [
                            'text'        => 'Un vrai agent Mobile Money peut avoir besoin de ton code PIN pour t\'aider.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'FAUX. Les agents agréés n\'ont jamais besoin de ton code PIN. Si quelqu\'un te le demande, c\'est un escroc.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                        [
                            'text'        => 'Quel numéro appeler pour signaler une arnaque MTN MoMo ?',
                            'type'        => 'qcm',
                            'explanation' => 'Le service client MTN est joignable au 1555.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => '1555', 'correct' => true],
                                ['text' => '688', 'correct' => false],
                                ['text' => '1111', 'correct' => false],
                                ['text' => '0700', 'correct' => false],
                            ],
                        ],
                    ],
                ],
            ],

            // ── Leçon 3 ───────────────────────────────────────────
            [
                'title'            => 'Bonnes pratiques pour sécuriser ton compte',
                'slug'             => 'bonnes-pratiques-mobile-money-' . Str::random(4),
                'order'            => 3,
                'duration_minutes' => 12,
                'is_published'     => true,
                'video_url'        => null,
                'content'          => <<<'HTML'
<h2>Comment sécuriser ton compte Mobile Money au quotidien</h2>

<h3>1. Choisir un bon code PIN</h3>
<ul>
  <li>❌ Évite : <code>1234</code>, <code>0000</code>, ta date de naissance</li>
  <li>✅ Choisis : un code que toi seul connais, sans logique évidente</li>
  <li>✅ Change ton PIN régulièrement (tous les 3 mois)</li>
</ul>

<h3>2. Vérifier chaque transaction</h3>
<ul>
  <li>Consulte ton solde régulièrement via <code>#155#</code> ou l'application</li>
  <li>Lis attentivement les SMS de confirmation avant de valider</li>
  <li>Vérifie le nom du destinataire avant d'envoyer</li>
</ul>

<h3>3. Protéger ton téléphone</h3>
<ul>
  <li>Active le verrouillage automatique (code ou empreinte digitale)</li>
  <li>Ne prête pas ton téléphone déverrouillé</li>
  <li>En cas de perte, bloque ton SIM immédiatement chez l'opérateur</li>
</ul>

<h3>4. Faire attention aux lieux publics</h3>
<ul>
  <li>Ne saisis jamais ton code PIN devant des inconnus</li>
  <li>Méfie-toi des personnes qui regardent par-dessus ton épaule</li>
  <li>Évite les transactions dans des endroits trop fréquentés</li>
</ul>

<h3>5. En cas de problème</h3>
<table>
  <thead><tr><th>Situation</th><th>Action</th></tr></thead>
  <tbody>
    <tr><td>Code PIN compromis</td><td>Change-le immédiatement via USSD</td></tr>
    <tr><td>Transaction non autorisée</td><td>Appelle le service client dans l'heure</td></tr>
    <tr><td>Téléphone volé</td><td>Bloque le SIM et le compte Mobile Money</td></tr>
    <tr><td>Arnaque subie</td><td>Signale à la PLCC au 1111</td></tr>
  </tbody>
</table>
HTML,
                'quiz' => [
                    'title'              => 'Quiz Final — Sécurité Mobile Money',
                    'description'        => 'Quiz de validation du module. Score minimum : 70%.',
                    'passing_score'      => 70,
                    'time_limit_minutes' => 12,
                    'max_attempts'       => 3,
                    'is_published'       => true,
                    'questions' => [
                        [
                            'text'        => 'Lequel de ces codes PIN est le moins sécurisé ?',
                            'type'        => 'qcm',
                            'explanation' => '1234 est le code PIN le plus utilisé et donc le plus facile à deviner.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => '1234', 'correct' => true],
                                ['text' => '7392', 'correct' => false],
                                ['text' => '8514', 'correct' => false],
                                ['text' => '2951', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Que faire en priorité si ton téléphone contenant ton compte Mobile Money est volé ?',
                            'type'        => 'qcm',
                            'explanation' => 'Bloquer le SIM empêche le voleur d\'accéder à ton compte Mobile Money.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Attendre que quelqu\'un le retrouve', 'correct' => false],
                                ['text' => 'Bloquer le SIM chez l\'opérateur et le compte Mobile Money', 'correct' => true],
                                ['text' => 'Changer de numéro de téléphone', 'correct' => false],
                                ['text' => 'Poster sur les réseaux sociaux', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Il est prudent de saisir son code PIN Mobile Money dans un lieu bondé.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'FAUX. Il faut éviter de saisir son code PIN dans les endroits très fréquentés pour éviter d\'être espionné.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => false],
                                ['text' => 'Faux', 'correct' => true],
                            ],
                        ],
                        [
                            'text'        => 'À quelle fréquence est-il recommandé de changer son code PIN Mobile Money ?',
                            'type'        => 'qcm',
                            'explanation' => 'Changer son PIN tous les 3 mois réduit le risque en cas de compromission.',
                            'points'      => 1,
                            'answers'     => [
                                ['text' => 'Jamais, si on s\'en souvient bien', 'correct' => false],
                                ['text' => 'Tous les 3 mois environ', 'correct' => true],
                                ['text' => 'Tous les 5 ans', 'correct' => false],
                                ['text' => 'Seulement après un problème', 'correct' => false],
                            ],
                        ],
                        [
                            'text'        => 'Avant d\'envoyer de l\'argent, tu dois vérifier le nom du destinataire affiché.',
                            'type'        => 'vrai_faux',
                            'explanation' => 'VRAI. Une erreur de numéro peut envoyer l\'argent à la mauvaise personne. Toujours vérifier le nom confirmé avant de valider.',
                            'points'      => 2,
                            'answers'     => [
                                ['text' => 'Vrai', 'correct' => true],
                                ['text' => 'Faux', 'correct' => false],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
