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
        $admin      = Role::firstOrCreate(['name'=>'admin'],      ['display_name'=>'Administrateur']);
        $etab       = Role::firstOrCreate(['name'=>'etablissement'], ['display_name'=>'Établissement']);
        $eleve      = Role::firstOrCreate(['name'=>'eleve'],      ['display_name'=>'Élève']);
        $etudiant   = Role::firstOrCreate(['name'=>'etudiant'],   ['display_name'=>'Étudiant']);
        $enseignant = Role::firstOrCreate(['name'=>'enseignant'], ['display_name'=>'Enseignant']);

        // ── Établissements ──
        $lycee = Etablissement::firstOrCreate(['name'=>'Lycée Moderne d\'Abidjan'], ['type'=>'lycee','city'=>'Abidjan']);
        $univ  = Etablissement::firstOrCreate(['name'=>'ISPA Abidjan'],             ['type'=>'universite','city'=>'Abidjan']);

        // ── Utilisateurs ──
        User::firstOrCreate(['email'=>'admin@ispa-cyber.ci'], [
            'name'=>'Admin ISPA',
            'password'=>Hash::make('AdminIspa@2024!'),
            'role_id'=>$admin->id,
            'is_active'=>true,
        ]);
        User::firstOrCreate(['email'=>'lycee@ispa-cyber.ci'], [
            'name'=>'Responsable Lycée',
            'password'=>Hash::make('LyceeIspa@2024!'),
            'role_id'=>$etab->id,
            'etablissement_id'=>$lycee->id,
            'is_active'=>true,
        ]);
        User::firstOrCreate(['email'=>'ispa@ispa-cyber.ci'], [
            'name'=>'Responsable ISPA',
            'password'=>Hash::make('IspaAdmin@2024!'),
            'role_id'=>$etab->id,
            'etablissement_id'=>$univ->id,
            'is_active'=>true,
        ]);
        User::firstOrCreate(['email'=>'prof@ispa-cyber.ci'], [
            'name'=>'Prof. Koné',
            'password'=>Hash::make('ProfKone@2024!'),
            'role_id'=>$enseignant->id,
            'etablissement_id'=>$lycee->id,
            'is_active'=>true,
        ]);

        $s1 = User::firstOrCreate(['email'=>'jean@ispa-cyber.ci'], [
            'name'=>'Kouamé Jean',
            'password'=>Hash::make('JeanKouame@2024!'),
            'role_id'=>$etudiant->id,
            'etablissement_id'=>$univ->id,
            'points'=>150,'level'=>2,
            'is_active'=>true,
        ]);
        $s2 = User::firstOrCreate(['email'=>'aicha@ispa-cyber.ci'], [
            'name'=>'Aïcha Traoré',
            'password'=>Hash::make('AichaTraore@2024!'),
            'role_id'=>$eleve->id,
            'etablissement_id'=>$lycee->id,
            'points'=>320,'level'=>3,
            'is_active'=>true,
        ]);

        $classe = Classe::firstOrCreate(
            ['name'=>'Terminale D','etablissement_id'=>$lycee->id],
            ['level'=>'terminale']
        );
        $classe->students()->syncWithoutDetaching([$s2->id]);

        // ── Badges ──
        foreach([
            ['name'=>'Premier Quiz Réussi','slug'=>'premier-quiz','description'=>'Premier quiz réussi !','icon'=>'🎯','category'=>'quiz'],
            ['name'=>'Expert Phishing','slug'=>'expert-phishing','description'=>'Module Phishing complété à 100%','icon'=>'🎣','category'=>'progression'],
            ['name'=>'Champion Cybersécurité','slug'=>'champion-cybersecurite','description'=>'Tous les modules complétés','icon'=>'🏆','category'=>'special'],
            ['name'=>'Assidu','slug'=>'assidu','description'=>'10 quiz réussis','icon'=>'⭐','category'=>'progression'],
            ['name'=>'Protecteur Mobile Money','slug'=>'protecteur-mobile-money','description'=>'Module Mobile Money complété','icon'=>'📱','category'=>'progression'],
            ['name'=>'Cyber Détective','slug'=>'cyber-detective','description'=>'Premier signalement validé','icon'=>'🔍','category'=>'special'],
            ['name'=>'CTF Master','slug'=>'ctf-master','description'=>'5 challenges CTF réussis','icon'=>'🏁','category'=>'special'],
        ] as $b) Badge::firstOrCreate(['slug'=>$b['slug']], $b);

        // ── Modules ──
        if (Module::count() === 0) {
            $this->seedModules();
        }
    }

    private function seedModules(): void
    {
        $modulesData = [
            ['title'=>'Introduction à la cybersécurité','slug'=>'introduction-cybersecurite','description'=>'Découvrez les bases de la cybersécurité : menaces, bonnes pratiques, et enjeux pour la Côte d\'Ivoire.','level'=>'tous','order'=>1,'duration_hours'=>2,'is_published'=>true,'lessons'=>[['title'=>'Qu\'est-ce que la cybersécurité ?','slug'=>'quest-ce-que-la-cybersecurite','content'=>'<h2>La cybersécurité en Côte d\'Ivoire</h2><p>La cybersécurité est l\'ensemble des pratiques, technologies et processus conçus pour protéger les systèmes informatiques contre les attaques numériques.</p><h3>Les 3 piliers (CIA)</h3><p><strong>Confidentialité</strong> : Seules les personnes autorisées accèdent aux données.</p><p><strong>Intégrité</strong> : Les données ne sont pas modifiées sans autorisation.</p><p><strong>Disponibilité</strong> : Les systèmes restent accessibles.</p><h3>Menaces en Côte d\'Ivoire</h3><ul><li>Arnaques par SMS et appels frauduleux</li><li>Phishing sur WhatsApp et Facebook</li><li>Fraude au Mobile Money</li><li>Usurpation d\'identité</li></ul>','order'=>1,'duration_minutes'=>10,'is_published'=>true,'quiz'=>['title'=>'Quiz : Bases de la cybersécurité','passing_score'=>70,'time_limit_minutes'=>10,'max_attempts'=>3,'is_published'=>true,'questions'=>[['text'=>'Que signifie la cybersécurité ?','type'=>'qcm','explanation'=>'La cybersécurité protège les systèmes et données.','answers'=>[['text'=>'Protection des systèmes contre les attaques numériques','correct'=>true],['text'=>'Réparation des ordinateurs','correct'=>false],['text'=>'Vente d\'antivirus','correct'=>false],['text'=>'Développement web','correct'=>false]]],['text'=>'Quels sont les 3 piliers de la cybersécurité ?','type'=>'qcm','explanation'=>'CIA : Confidentialité, Intégrité, Disponibilité.','answers'=>[['text'=>'Confidentialité, Intégrité, Disponibilité','correct'=>true],['text'=>'Connexion, Internet, Application','correct'=>false],['text'=>'Code, Infrastructure, Analyse','correct'=>false]]],['text'=>'Le phishing est courant en Côte d\'Ivoire.','type'=>'vrai_faux','explanation'=>'Le phishing via SMS et WhatsApp est très répandu en CI.','answers'=>[['text'=>'Vrai','correct'=>true],['text'=>'Faux','correct'=>false]]]]]],['title'=>'Les menaces en Afrique de l\'Ouest','slug'=>'menaces-afrique-ouest','content'=>'<h2>Menaces numériques en Afrique de l\'Ouest</h2><h3>1. Arnaques Mobile Money</h3><p>SMS frauduleux prétendant que vous avez gagné un lot.</p><h3>2. Phishing réseaux sociaux</h3><p>Faux profils Facebook/WhatsApp pour voler argent ou données.</p><h3>3. Faux sites e-commerce</h3><p>Sites imitant des boutiques connues pour voler vos données bancaires.</p><h3>4. Cyberharcèlement</h3><p>Diffusion de contenus privés et intimidation en ligne.</p>','order'=>2,'duration_minutes'=>12,'is_published'=>true,'quiz'=>null]]],
            ['title'=>'Mots de passe sécurisés','slug'=>'mots-de-passe-securises','description'=>'Apprenez à créer et gérer des mots de passe robustes.','level'=>'tous','order'=>2,'duration_hours'=>1,'is_published'=>true,'lessons'=>[['title'=>'Créer un mot de passe fort','slug'=>'creer-mot-de-passe-fort','content'=>'<h2>Comment créer un mot de passe fort ?</h2><h3>Les règles d\'or</h3><ul><li><strong>Minimum 12 caractères</strong></li><li><strong>Mélangez</strong> : majuscules, minuscules, chiffres, symboles</li><li><strong>Évitez</strong> : votre nom, date de naissance, "123456"</li><li><strong>Un mot de passe unique</strong> par compte</li></ul>','order'=>1,'duration_minutes'=>8,'is_published'=>true,'quiz'=>['title'=>'Quiz : Mots de passe','passing_score'=>70,'time_limit_minutes'=>8,'max_attempts'=>3,'is_published'=>true,'questions'=>[['text'=>'Longueur minimale recommandée pour un mot de passe ?','type'=>'qcm','explanation'=>'12 caractères minimum.','answers'=>[['text'=>'4','correct'=>false],['text'=>'8','correct'=>false],['text'=>'12','correct'=>true],['text'=>'6','correct'=>false]]],['text'=>'Utiliser le même mot de passe partout est sécuritaire.','type'=>'vrai_faux','explanation'=>'Un seul compte compromis expose tous vos comptes.','answers'=>[['text'=>'Vrai','correct'=>false],['text'=>'Faux','correct'=>true]]]]]]]],
            ['title'=>'Détection du phishing','slug'=>'detection-phishing','description'=>'Identifiez et évitez les tentatives de phishing.','level'=>'tous','order'=>3,'duration_hours'=>2,'is_published'=>true,'lessons'=>[['title'=>'Reconnaître un email de phishing','slug'=>'reconnaitre-email-phishing','content'=>'<h2>Comment reconnaître un email de phishing ?</h2><h3>Signaux d\'alerte</h3><ul><li><strong>Expéditeur suspect</strong></li><li><strong>Urgence artificielle</strong></li><li><strong>Fautes d\'orthographe</strong></li><li><strong>Liens suspects</strong></li></ul>','order'=>1,'duration_minutes'=>15,'is_published'=>true,'quiz'=>['title'=>'Quiz : Détection du phishing','passing_score'=>70,'time_limit_minutes'=>10,'max_attempts'=>3,'is_published'=>true,'questions'=>[['text'=>'Un SMS dit : "Votre compte Orange Money est bloqué." Que faites-vous ?','type'=>'qcm','explanation'=>'Contactez toujours le service client officiel.','answers'=>[['text'=>'J\'appelle le numéro','correct'=>false],['text'=>'Je contacte le service client officiel','correct'=>true],['text'=>'J\'envoie mon code secret','correct'=>false]]],['text'=>'Une entreprise légitime peut demander votre mot de passe par email.','type'=>'vrai_faux','explanation'=>'Aucune entreprise ne demande votre mot de passe par email.','answers'=>[['text'=>'Vrai','correct'=>false],['text'=>'Faux','correct'=>true]]]]]]]],
            ['title'=>'Sécurité Mobile Money','slug'=>'securite-mobile-money','description'=>'Protégez vos comptes MTN MoMo, Orange Money et Wave.','level'=>'tous','order'=>4,'duration_hours'=>2,'is_published'=>true,'lessons'=>[['title'=>'Les arnaques Mobile Money en CI','slug'=>'arnaques-mobile-money-ci','content'=>'<h2>Les arnaques Mobile Money les plus courantes</h2><h3>1. L\'arnaque au faux transfert</h3><p>L\'escroc vous appelle en disant qu\'il a fait un transfert par erreur.</p><h3>Comment se protéger</h3><ul><li>Ne JAMAIS partager votre code secret</li><li>Vérifier votre solde AVANT de renvoyer de l\'argent</li></ul>','order'=>1,'duration_minutes'=>12,'is_published'=>true,'quiz'=>['title'=>'Quiz : Sécurité Mobile Money','passing_score'=>70,'time_limit_minutes'=>10,'max_attempts'=>3,'is_published'=>true,'questions'=>[['text'=>'Quelqu\'un vous appelle en disant avoir fait un transfert par erreur. Que faites-vous ?','type'=>'qcm','explanation'=>'Vérifiez toujours votre solde avant de renvoyer de l\'argent.','answers'=>[['text'=>'Je renvoie l\'argent immédiatement','correct'=>false],['text'=>'Je vérifie mon solde puis j\'appelle le service client','correct'=>true],['text'=>'Je donne mon code secret','correct'=>false]]],['text'=>'Un agent MTN peut vous demander votre code secret par téléphone.','type'=>'vrai_faux','explanation'=>'JAMAIS.','answers'=>[['text'=>'Vrai','correct'=>false],['text'=>'Faux','correct'=>true]]]]]]]],
            ['title'=>'Réseaux sociaux et vie privée','slug'=>'reseaux-sociaux-vie-privee','description'=>'Protégez votre vie privée sur Facebook, WhatsApp, Instagram et TikTok.','level'=>'tous','order'=>5,'duration_hours'=>2,'is_published'=>true,'lessons'=>[['title'=>'Sécuriser vos comptes sociaux','slug'=>'securiser-comptes-sociaux','content'=>'<h2>Protégez vos réseaux sociaux</h2><h3>Facebook</h3><ul><li>Activez la validation en 2 étapes</li><li>N\'acceptez pas les demandes d\'amis d\'inconnus</li></ul><h3>WhatsApp</h3><ul><li>Activez la vérification en 2 étapes</li><li>Ne partagez jamais votre code de vérification</li></ul>','order'=>1,'duration_minutes'=>14,'is_published'=>true,'quiz'=>['title'=>'Quiz : Réseaux sociaux','passing_score'=>70,'time_limit_minutes'=>10,'max_attempts'=>3,'is_published'=>true,'questions'=>[['text'=>'Quelle est la première chose à activer sur WhatsApp ?','type'=>'qcm','explanation'=>'La vérification en 2 étapes protège votre compte.','answers'=>[['text'=>'Le mode sombre','correct'=>false],['text'=>'La vérification en 2 étapes','correct'=>true],['text'=>'Les messages éphémères','correct'=>false]]],['text'=>'Accepter les demandes d\'amis d\'inconnus sur Facebook est sans risque.','type'=>'vrai_faux','explanation'=>'Les faux profils collectent vos infos personnelles.','answers'=>[['text'=>'Vrai','correct'=>false],['text'=>'Faux','correct'=>true]]]]]]]]
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
