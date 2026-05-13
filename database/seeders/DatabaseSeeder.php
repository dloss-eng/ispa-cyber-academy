<?php
namespace Database\Seeders;
use App\Models\{Role, User, Etablissement, Classe, Module, Lesson, Quiz, Question, Answer, Badge};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 4 Rôles ──
        $admin = Role::create(['name'=>'admin','display_name'=>'Administrateur']);
        $etab = Role::create(['name'=>'etablissement','display_name'=>'Établissement']);
        $eleve = Role::create(['name'=>'eleve','display_name'=>'Élève']);
        $etudiant = Role::create(['name'=>'etudiant','display_name'=>'Étudiant']);
        $enseignant = Role::create(['name'=>'enseignant','display_name'=>'Enseignant']);

        // ── Établissements ──
        $lycee = Etablissement::create(['name'=>'Lycée Moderne d\'Abidjan','type'=>'lycee','city'=>'Abidjan']);
        $univ = Etablissement::create(['name'=>'ISPA Abidjan','type'=>'universite','city'=>'Abidjan']);

        // ── Utilisateurs ──
        User::create(['name'=>'Admin ISPA','email'=>'admin@ispa-cyber.ci','password'=>Hash::make('admin12345'),'role_id'=>$admin->id]);
        User::create(['name'=>'Responsable Lycée','email'=>'lycee@ispa-cyber.ci','password'=>Hash::make('lycee12345'),'role_id'=>$etab->id,'etablissement_id'=>$lycee->id]);
        User::create(['name'=>'Responsable ISPA','email'=>'ispa@ispa-cyber.ci','password'=>Hash::make('ispa12345'),'role_id'=>$etab->id,'etablissement_id'=>$univ->id]);
        User::create(['name'=>'Prof. Koné','email'=>'prof@ispa-cyber.ci','password'=>Hash::make('prof12345'),'role_id'=>$enseignant->id,'etablissement_id'=>$lycee->id]);

        $s1 = User::create(['name'=>'Kouamé Jean','email'=>'jean@ispa-cyber.ci','password'=>Hash::make('etudiant123'),'role_id'=>$etudiant->id,'etablissement_id'=>$univ->id,'points'=>150,'level'=>2]);
        $s2 = User::create(['name'=>'Aïcha Traoré','email'=>'aicha@ispa-cyber.ci','password'=>Hash::make('eleve123'),'role_id'=>$eleve->id,'etablissement_id'=>$lycee->id,'points'=>320,'level'=>3]);

        $classe = Classe::create(['name'=>'Terminale D','etablissement_id'=>$lycee->id,'level'=>'terminale']);
        $classe->students()->attach([$s2->id]);

        // ── Badges (7) ──
        foreach([
            ['name'=>'Premier Quiz Réussi','slug'=>'premier-quiz','description'=>'Premier quiz réussi !','icon'=>'🎯','category'=>'quiz'],
            ['name'=>'Expert Phishing','slug'=>'expert-phishing','description'=>'Module Phishing complété à 100%','icon'=>'🎣','category'=>'progression'],
            ['name'=>'Champion Cybersécurité','slug'=>'champion-cybersecurite','description'=>'Tous les modules complétés','icon'=>'🏆','category'=>'special'],
            ['name'=>'Assidu','slug'=>'assidu','description'=>'10 quiz réussis','icon'=>'⭐','category'=>'progression'],
            ['name'=>'Protecteur Mobile Money','slug'=>'protecteur-mobile-money','description'=>'Module Mobile Money complété','icon'=>'📱','category'=>'progression'],
            ['name'=>'Cyber Détective','slug'=>'cyber-detective','description'=>'Premier signalement validé','icon'=>'🔍','category'=>'special'],
            ['name'=>'CTF Master','slug'=>'ctf-master','description'=>'5 challenges CTF réussis','icon'=>'🏁','category'=>'special'],
        ] as $b) Badge::create($b);

        // ── 5 Modules ──
        $this->seedModules();
    }

    private function seedModules(): void
    {
        $modulesData = [
            [
                'title'=>'Introduction à la cybersécurité','slug'=>'introduction-cybersecurite',
                'description'=>'Découvrez les bases de la cybersécurité : menaces, bonnes pratiques, et enjeux pour la Côte d\'Ivoire.',
                'level'=>'tous','order'=>1,'duration_hours'=>2,'is_published'=>true,
                'lessons'=>[
                    ['title'=>'Qu\'est-ce que la cybersécurité ?','slug'=>'quest-ce-que-la-cybersecurite',
                     'content'=>'<h2>La cybersécurité en Côte d\'Ivoire</h2><p>La cybersécurité est l\'ensemble des pratiques, technologies et processus conçus pour protéger les systèmes informatiques contre les attaques numériques.</p><h3>Les 3 piliers (CIA)</h3><p><strong>Confidentialité</strong> : Seules les personnes autorisées accèdent aux données.</p><p><strong>Intégrité</strong> : Les données ne sont pas modifiées sans autorisation.</p><p><strong>Disponibilité</strong> : Les systèmes restent accessibles.</p><h3>Menaces en Côte d\'Ivoire</h3><ul><li>Arnaques par SMS et appels frauduleux</li><li>Phishing sur WhatsApp et Facebook</li><li>Fraude au Mobile Money</li><li>Usurpation d\'identité</li></ul>',
                     'order'=>1,'duration_minutes'=>10,'is_published'=>true,
                     'quiz'=>['title'=>'Quiz : Bases de la cybersécurité','passing_score'=>70,'time_limit_minutes'=>10,'max_attempts'=>3,'is_published'=>true,
                       'questions'=>[
                           ['text'=>'Que signifie la cybersécurité ?','type'=>'qcm','explanation'=>'La cybersécurité protège les systèmes et données.','answers'=>[['text'=>'Protection des systèmes contre les attaques numériques','correct'=>true],['text'=>'Réparation des ordinateurs','correct'=>false],['text'=>'Vente d\'antivirus','correct'=>false],['text'=>'Développement web','correct'=>false]]],
                           ['text'=>'Quels sont les 3 piliers de la cybersécurité ?','type'=>'qcm','explanation'=>'CIA : Confidentialité, Intégrité, Disponibilité.','answers'=>[['text'=>'Confidentialité, Intégrité, Disponibilité','correct'=>true],['text'=>'Connexion, Internet, Application','correct'=>false],['text'=>'Code, Infrastructure, Analyse','correct'=>false]]],
                           ['text'=>'Le phishing est courant en Côte d\'Ivoire.','type'=>'vrai_faux','explanation'=>'Le phishing via SMS et WhatsApp est très répandu en CI.','answers'=>[['text'=>'Vrai','correct'=>true],['text'=>'Faux','correct'=>false]]],
                       ]]],
                    ['title'=>'Les menaces en Afrique de l\'Ouest','slug'=>'menaces-afrique-ouest',
                     'content'=>'<h2>Menaces numériques en Afrique de l\'Ouest</h2><h3>1. Arnaques Mobile Money</h3><p>SMS frauduleux prétendant que vous avez gagné un lot.</p><h3>2. Phishing réseaux sociaux</h3><p>Faux profils Facebook/WhatsApp pour voler argent ou données.</p><h3>3. Faux sites e-commerce</h3><p>Sites imitant des boutiques connues pour voler vos données bancaires.</p><h3>4. Cyberharcèlement</h3><p>Diffusion de contenus privés et intimidation en ligne.</p>',
                     'order'=>2,'duration_minutes'=>12,'is_published'=>true,'quiz'=>null],
                ],
            ],
            [
                'title'=>'Mots de passe sécurisés','slug'=>'mots-de-passe-securises',
                'description'=>'Apprenez à créer et gérer des mots de passe robustes.',
                'level'=>'tous','order'=>2,'duration_hours'=>1,'is_published'=>true,
                'lessons'=>[
                    ['title'=>'Créer un mot de passe fort','slug'=>'creer-mot-de-passe-fort',
                     'content'=>'<h2>Comment créer un mot de passe fort ?</h2><h3>Les règles d\'or</h3><ul><li><strong>Minimum 12 caractères</strong></li><li><strong>Mélangez</strong> : majuscules, minuscules, chiffres, symboles</li><li><strong>Évitez</strong> : votre nom, date de naissance, "123456"</li><li><strong>Un mot de passe unique</strong> par compte</li></ul><h3>La technique de la phrase</h3><p>"J\'aime le café à Abidjan en 2024!" → <code>J@lc@A2024!</code></p>',
                     'order'=>1,'duration_minutes'=>8,'is_published'=>true,
                     'quiz'=>['title'=>'Quiz : Mots de passe','passing_score'=>70,'time_limit_minutes'=>8,'max_attempts'=>3,'is_published'=>true,
                       'questions'=>[
                           ['text'=>'Longueur minimale recommandée pour un mot de passe ?','type'=>'qcm','explanation'=>'12 caractères minimum.','answers'=>[['text'=>'4','correct'=>false],['text'=>'8','correct'=>false],['text'=>'12','correct'=>true],['text'=>'6','correct'=>false]]],
                           ['text'=>'Utiliser le même mot de passe partout est sécuritaire.','type'=>'vrai_faux','explanation'=>'Un seul compte compromis expose tous vos comptes.','answers'=>[['text'=>'Vrai','correct'=>false],['text'=>'Faux','correct'=>true]]],
                       ]]],
                ],
            ],
            [
                'title'=>'Détection du phishing','slug'=>'detection-phishing',
                'description'=>'Identifiez et évitez les tentatives de phishing par email, SMS et réseaux sociaux.',
                'level'=>'tous','order'=>3,'duration_hours'=>2,'is_published'=>true,
                'lessons'=>[
                    ['title'=>'Reconnaître un email de phishing','slug'=>'reconnaitre-email-phishing',
                     'content'=>'<h2>Comment reconnaître un email de phishing ?</h2><h3>Signaux d\'alerte</h3><ul><li><strong>Expéditeur suspect</strong></li><li><strong>Urgence artificielle</strong> : "Compte fermé dans 24h !"</li><li><strong>Fautes d\'orthographe</strong></li><li><strong>Liens suspects</strong></li><li><strong>Demande d\'informations sensibles</strong></li></ul><h3>Cas pratique SMS Mobile Money</h3><p>"Félicitations ! Vous avez gagné 500.000 FCFA sur MTN MoMo. Envoyez votre code secret au 0700000000."</p><p>⚠️ <strong>ARNAQUE</strong> : MTN ne demande jamais votre code secret !</p>',
                     'order'=>1,'duration_minutes'=>15,'is_published'=>true,
                     'quiz'=>['title'=>'Quiz : Détection du phishing','passing_score'=>70,'time_limit_minutes'=>10,'max_attempts'=>3,'is_published'=>true,
                       'questions'=>[
                           ['text'=>'Un SMS dit : "Votre compte Orange Money est bloqué. Appelez le 0700000000." Que faites-vous ?','type'=>'qcm','explanation'=>'Contactez toujours le service client officiel.','answers'=>[['text'=>'J\'appelle le numéro','correct'=>false],['text'=>'Je contacte le service client officiel','correct'=>true],['text'=>'J\'envoie mon code secret','correct'=>false],['text'=>'Je transfère le message','correct'=>false]]],
                           ['text'=>'Une entreprise légitime peut demander votre mot de passe par email.','type'=>'vrai_faux','explanation'=>'Aucune entreprise ne demande votre mot de passe par email.','answers'=>[['text'=>'Vrai','correct'=>false],['text'=>'Faux','correct'=>true]]],
                       ]]],
                ],
            ],
            [
                'title'=>'Sécurité Mobile Money','slug'=>'securite-mobile-money',
                'description'=>'Protégez vos comptes MTN MoMo, Orange Money et Wave contre les arnaques.',
                'level'=>'tous','order'=>4,'duration_hours'=>2,'is_published'=>true,
                'lessons'=>[
                    ['title'=>'Les arnaques Mobile Money en CI','slug'=>'arnaques-mobile-money-ci',
                     'content'=>'<h2>Les arnaques Mobile Money les plus courantes</h2><h3>1. L\'arnaque au faux transfert</h3><p>L\'escroc vous appelle en disant qu\'il a fait un transfert par erreur sur votre compte et vous demande de renvoyer l\'argent. En réalité, aucun transfert n\'a été fait.</p><h3>2. Le faux agent</h3><p>Quelqu\'un se fait passer pour un agent MTN/Orange et vous demande votre code secret pour "vérifier votre compte".</p><h3>3. Le SMS de gain</h3><p>"Vous avez gagné 1.000.000 FCFA ! Appelez le 0700000000." C\'est TOUJOURS une arnaque.</p><h3>Comment se protéger</h3><ul><li>Ne JAMAIS partager votre code secret</li><li>Vérifier votre solde AVANT de renvoyer de l\'argent</li><li>Appeler le service client officiel en cas de doute</li><li>Ne jamais appeler les numéros dans les SMS suspects</li></ul>',
                     'order'=>1,'duration_minutes'=>12,'is_published'=>true,
                     'quiz'=>['title'=>'Quiz : Sécurité Mobile Money','passing_score'=>70,'time_limit_minutes'=>10,'max_attempts'=>3,'is_published'=>true,
                       'questions'=>[
                           ['text'=>'Quelqu\'un vous appelle en disant avoir fait un transfert par erreur. Que faites-vous ?','type'=>'qcm','explanation'=>'Vérifiez toujours votre solde avant de renvoyer de l\'argent.','answers'=>[['text'=>'Je renvoie l\'argent immédiatement','correct'=>false],['text'=>'Je vérifie mon solde puis j\'appelle le service client','correct'=>true],['text'=>'Je donne mon code secret','correct'=>false]]],
                           ['text'=>'Un agent MTN peut vous demander votre code secret par téléphone.','type'=>'vrai_faux','explanation'=>'JAMAIS. Aucun agent ne demande votre code secret.','answers'=>[['text'=>'Vrai','correct'=>false],['text'=>'Faux','correct'=>true]]],
                       ]]],
                ],
            ],
            [
                'title'=>'Réseaux sociaux et vie privée','slug'=>'reseaux-sociaux-vie-privee',
                'description'=>'Protégez votre vie privée sur Facebook, WhatsApp, Instagram et TikTok.',
                'level'=>'tous','order'=>5,'duration_hours'=>2,'is_published'=>true,
                'lessons'=>[
                    ['title'=>'Sécuriser vos comptes sociaux','slug'=>'securiser-comptes-sociaux',
                     'content'=>'<h2>Protégez vos réseaux sociaux</h2><h3>Facebook</h3><ul><li>Activez la validation en 2 étapes</li><li>Vérifiez les paramètres de confidentialité</li><li>N\'acceptez pas les demandes d\'amis d\'inconnus</li></ul><h3>WhatsApp</h3><ul><li>Activez la vérification en 2 étapes</li><li>Ne partagez jamais votre code de vérification</li><li>Méfiez-vous des liens suspects dans les groupes</li></ul><h3>Instagram / TikTok</h3><ul><li>Passez en compte privé</li><li>Ne partagez pas d\'informations personnelles (adresse, école)</li><li>Bloquez et signalez les comptes suspects</li></ul>',
                     'order'=>1,'duration_minutes'=>14,'is_published'=>true,
                     'quiz'=>['title'=>'Quiz : Réseaux sociaux','passing_score'=>70,'time_limit_minutes'=>10,'max_attempts'=>3,'is_published'=>true,
                       'questions'=>[
                           ['text'=>'Quelle est la première chose à activer sur WhatsApp pour se protéger ?','type'=>'qcm','explanation'=>'La vérification en 2 étapes protège votre compte.','answers'=>[['text'=>'Le mode sombre','correct'=>false],['text'=>'La vérification en 2 étapes','correct'=>true],['text'=>'Les messages éphémères','correct'=>false]]],
                           ['text'=>'Accepter les demandes d\'amis d\'inconnus sur Facebook est sans risque.','type'=>'vrai_faux','explanation'=>'Les faux profils servent souvent à collecter vos infos personnelles.','answers'=>[['text'=>'Vrai','correct'=>false],['text'=>'Faux','correct'=>true]]],
                       ]]],
                ],
            ],
            [
                'title'=>'Challenges CTF','slug'=>'challenges-ctf',
                'description'=>'Capture The Flag : jouez le role d un cyber-detective !',
                'level'=>'tous','order'=>6,'duration_hours'=>3,'is_published'=>true,
                'lessons'=>[
                    ['title'=>'CTF 1 : SMS suspect','slug'=>'ctf-sms-suspect','content'=>'<h2>Challenge CTF</h2><p style="background:rgba(255,107,53,0.1);border:1px solid rgba(255,107,53,0.3);border-radius:10px;padding:16px;margin:20px 0"><strong>Message recu :</strong><br>Cher client, votre compte MTN MoMo sera suspendu dans 24h. Envoyez votre code secret au 0700112233.</p><h3>Mission</h3><p>Identifiez les indices d arnaque.</p>','order'=>1,'duration_minutes'=>15,'is_published'=>true,'quiz'=>['title'=>'CTF SMS','passing_score'=>80,'time_limit_minutes'=>10,'max_attempts'=>5,'is_published'=>true,'questions'=>[['text'=>'Premier indice d arnaque ?','type'=>'qcm','explanation'=>'MTN ne demande JAMAIS votre code.','answers'=>[['text'=>'Demande le code secret','correct'=>true],['text'=>'SMS trop long','correct'=>false],['text'=>'Contient emojis','correct'=>false]]],['text'=>'0700112233 est officiel MTN.','type'=>'vrai_faux','explanation'=>'Les numeros officiels MTN ne commencent pas par 0700.','answers'=>[['text'=>'Vrai','correct'=>false],['text'=>'Faux','correct'=>true]]]]]],
                    ['title'=>'CTF 2 : Email phishing','slug'=>'ctf-email-phishing','content'=>'<h2>Challenge CTF</h2><p style="background:rgba(75,123,255,0.1);border:1px solid rgba(75,123,255,0.3);border-radius:10px;padding:16px;margin:20px 0"><strong>Email de : service-orange-m0ney@gmail.com</strong><br>Activite suspecte detectee. Cliquez : http://orange-ci-secure.ml/verify</p>','order'=>2,'duration_minutes'=>15,'is_published'=>true,'quiz'=>['title'=>'CTF Email','passing_score'=>80,'time_limit_minutes'=>10,'max_attempts'=>5,'is_published'=>true,'questions'=>[['text'=>'Pourquoi l adresse est suspecte ?','type'=>'qcm','explanation'=>'Orange n utilise pas gmail.com.','answers'=>[['text'=>'Utilise gmail.com','correct'=>true],['text'=>'Trop longue','correct'=>false],['text'=>'Contient service','correct'=>false]]],['text'=>'Le domaine .ml est officiel Orange CI.','type'=>'vrai_faux','explanation'=>'.ml est le Mali.','answers'=>[['text'=>'Vrai','correct'=>false],['text'=>'Faux','correct'=>true]]]]]],
                ],
            ],
        ];

        foreach ($modulesData as $mData) {
            $lessons = $mData['lessons']; unset($mData['lessons']);
            $module = Module::create($mData);
            foreach ($lessons as $lData) {
                $quizData = $lData['quiz'] ?? null; unset($lData['quiz']);
                $lData['module_id'] = $module->id;
                $lesson = Lesson::create($lData);
                if ($quizData) {
                    $questions = $quizData['questions']; unset($quizData['questions']);
                    $quizData['lesson_id'] = $lesson->id;
                    $quiz = Quiz::create($quizData);
                    foreach ($questions as $i => $qData) {
                        $answers = $qData['answers'];
                        $question = Question::create(['quiz_id'=>$quiz->id,'question_text'=>$qData['text'],'type'=>$qData['type'],'explanation'=>$qData['explanation']??null,'points'=>1,'order'=>$i]);
                        foreach ($answers as $j => $aData) {
                            Answer::create(['question_id'=>$question->id,'answer_text'=>$aData['text'],'is_correct'=>$aData['correct'],'order'=>$j]);
                        }
                    }
                }
            }
        }
    }
}
