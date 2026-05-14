<?php
// ════════════════════════════════════════════════════════════════════
//  ISPA Cyber Academy — Routes
//  ✅ Toutes les corrections appliquées :
//    - Rate limiting sur login, 2FA, forum, quiz, signalement
//    - Closures remplacées par méthodes de contrôleurs
//    - role:eleve,etudiant sur toutes les routes étudiantes
//    - Route contact.send présente
//    - route:cache compatible (0 closure)
//    - Notifications accessibles à tous les rôles connectés
//    - ✅ Routes CTF ajoutées (étudiant + admin + établissement)
// ════════════════════════════════════════════════════════════════════

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\{AuthController, TwoFactorController};
use App\Http\Controllers\Student\{DashboardController, CourseController, QuizController, CertificateController, NotificationController, SignalementController};
use App\Http\Controllers\Student\CtfController;                         // ✅ CTF étudiant
use App\Http\Controllers\Admin\DashboardController      as AdminDash;
use App\Http\Controllers\Admin\UserController           as AdminUser;
use App\Http\Controllers\Admin\ModuleController         as AdminModule;
use App\Http\Controllers\Admin\EtablissementController  as AdminEtab;
use App\Http\Controllers\Admin\BadgeController          as AdminBadge;
use App\Http\Controllers\Admin\CertificateController    as AdminCert;
use App\Http\Controllers\Admin\SignalementController    as AdminSignalement;
use App\Http\Controllers\Admin\PaymentController        as AdminPayment;
use App\Http\Controllers\Admin\CtfController            as AdminCtf;    // ✅ CTF admin
use App\Http\Controllers\Etablissement\DashboardController as EtabDash;
use App\Http\Controllers\Etablissement\PaymentController   as EtabPayment;
use App\Http\Controllers\Enseignant\DashboardController    as EnseignantDash;
use App\Http\Controllers\Forum\ForumController;

// ══════════════════════════════════════════════
//  ROUTES PUBLIQUES
// ══════════════════════════════════════════════

Route::get('/',                    [HomeController::class, 'index'])->name('home');
Route::get('/cours',               [HomeController::class, 'courses'])->name('public.courses');
Route::get('/cours/{module}',      [HomeController::class, 'courseDetail'])->name('public.course-detail');
Route::get('/verifier-certificat', [HomeController::class, 'verifyCertificate'])->name('verify.certificate');
Route::get('/api-docs',            [HomeController::class, 'apiDocs'])->name('api.docs');
Route::get('/contact',             [HomeController::class, 'contact'])->name('contact');
Route::get('/about',               [HomeController::class, 'about'])->name('about');

// ✅ Route POST contact — notifie les admins
Route::post('/contact', [HomeController::class, 'sendContact'])->name('contact.send');

// ══════════════════════════════════════════════
//  AUTHENTIFICATION
//  ✅ Rate limiting sur login et 2FA
// ══════════════════════════════════════════════

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/2fa',         [TwoFactorController::class, 'show'])->name('2fa.show');
Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify')->middleware('throttle:5,1');
Route::post('/2fa/resend', [TwoFactorController::class, 'resend'])->name('2fa.resend')->middleware('throttle:3,5');

// ══════════════════════════════════════════════
//  NOTIFICATIONS — tous les rôles connectés
//  ✅ Admin, étudiant, enseignant, établissement
// ══════════════════════════════════════════════

Route::middleware('auth')->group(function () {
    Route::get('/notifications',                      [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all',            [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
});

// ══════════════════════════════════════════════
//  ÉTUDIANT / ÉLÈVE
//  ✅ role:eleve,etudiant — admin ne peut plus
//     accéder au dashboard étudiant
// ══════════════════════════════════════════════

Route::middleware(['auth', 'role:eleve,etudiant'])->group(function () {

    Route::get('/dashboard',       [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/classement',      [DashboardController::class, 'leaderboard'])->name('leaderboard');
    Route::get('/mes-badges',      [DashboardController::class, 'badges'])->name('badges');
    Route::get('/mes-certificats', [DashboardController::class, 'certificates'])->name('certificates');

    Route::get('/mes-cours',                                   [CourseController::class, 'index'])->name('courses.index');
    Route::get('/mes-cours/{module}',                          [CourseController::class, 'show'])->name('courses.show');
    Route::get('/mes-cours/{module}/lecon/{lesson}',           [CourseController::class, 'lesson'])->name('courses.lesson');
    Route::post('/mes-cours/{module}/lecon/{lesson}/complete', [CourseController::class, 'completeLesson'])->name('courses.lesson.complete');
    Route::get('/mes-cours/{module}/{lesson}/ajax',            [CourseController::class, 'lessonAjax'])->name('courses.lesson.ajax');

    Route::get('/quiz/{quiz}',         [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])
        ->middleware('throttle:10,1')
        ->name('quiz.submit');
    Route::get('/quiz-resultat/{attempt}', [QuizController::class, 'result'])->name('quiz.result');

    Route::get('/certificat/{certificate}',          [CertificateController::class, 'show'])->name('certificate.show');
    Route::get('/certificat/{certificate}/download', [CertificateController::class, 'download'])->name('certificate.download');

    Route::get('/signalements',         [SignalementController::class, 'index'])->name('signalements.index');
    Route::get('/signalements/nouveau', [SignalementController::class, 'create'])->name('signalements.create');
    Route::post('/signalements',        [SignalementController::class, 'store'])
        ->middleware('throttle:5,10')
        ->name('signalements.store');

    // ✅ CTF — Challenges Capture The Flag
    Route::get('/ctf',                     [CtfController::class, 'index'])->name('ctf.index');
    Route::get('/ctf/classement',          [CtfController::class, 'leaderboard'])->name('ctf.leaderboard');
    Route::get('/ctf/{challenge}',         [CtfController::class, 'show'])->name('ctf.show');
    Route::post('/ctf/{challenge}/submit', [CtfController::class, 'submit'])
        ->middleware('throttle:10,5')
        ->name('ctf.submit');
    Route::post('/ctf/{challenge}/hint',   [CtfController::class, 'revealHint'])
        ->middleware('throttle:20,5')
        ->name('ctf.hint');
});

// ══════════════════════════════════════════════
//  FORUM (tous les rôles connectés)
//  ✅ Rate limiting sur store et reply
// ══════════════════════════════════════════════

Route::middleware('auth')->group(function () {
    Route::get('/forum',                [ForumController::class, 'index'])->name('forum.index');
    Route::get('/forum/nouveau',        [ForumController::class, 'create'])->name('forum.create');
    Route::post('/forum',               [ForumController::class, 'store'])
        ->middleware('throttle:10,10')
        ->name('forum.store');
    Route::get('/forum/{topic}',        [ForumController::class, 'show'])->name('forum.show');
    Route::post('/forum/{topic}/reply', [ForumController::class, 'reply'])
        ->middleware('throttle:20,10')
        ->name('forum.reply');
});

// ══════════════════════════════════════════════
//  PROFIL (tous rôles connectés)
// ══════════════════════════════════════════════

Route::middleware('auth')->group(function () {
    Route::get('/profil',        [HomeController::class, 'showProfile'])->name('profile');
    Route::put('/profil',        [HomeController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profil/{user}', [HomeController::class, 'publicProfile'])->name('profile.public');
});

// ══════════════════════════════════════════════
//  ADMIN — bloc unique fusionné
// ══════════════════════════════════════════════

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/dashboard', [AdminDash::class, 'index'])->name('dashboard');

    // Utilisateurs
    Route::resource('users', AdminUser::class)->except('show');

    // Modules + Leçons + Quiz
    Route::resource('modules', AdminModule::class)->except('show');
    Route::get('/modules/{module}/lessons/new',           [AdminModule::class, 'createLesson'])->name('modules.lessons.create');
    Route::post('/modules/{module}/lessons',              [AdminModule::class, 'storeLesson'])->name('modules.lessons.store');
    Route::get('/modules/{module}/lessons/{lesson}/edit', [AdminModule::class, 'editLesson'])->name('modules.lessons.edit');
    Route::put('/modules/{module}/lessons/{lesson}',      [AdminModule::class, 'updateLesson'])->name('modules.lessons.update');
    Route::delete('/modules/{module}/lessons/{lesson}',   [AdminModule::class, 'destroyLesson'])->name('modules.lessons.destroy');
    Route::get('/lessons/{lesson}/quiz/new',              [AdminModule::class, 'createQuiz'])->name('lessons.quiz.create');
    Route::post('/lessons/{lesson}/quiz',                 [AdminModule::class, 'storeQuiz'])->name('lessons.quiz.store');
    Route::get('/quiz/{quiz}/edit',                       [AdminModule::class, 'editQuiz'])->name('lessons.quiz.edit');
    Route::put('/quiz/{quiz}',                            [AdminModule::class, 'updateQuiz'])->name('lessons.quiz.update');
    Route::delete('/quiz/{quiz}',                         [AdminModule::class, 'destroyQuiz'])->name('lessons.quiz.destroy');
    Route::get('/modules/{module}/quiz/new',              [AdminModule::class, 'createModuleQuiz'])->name('modules.quiz.create');
    Route::post('/modules/{module}/quiz',                 [AdminModule::class, 'storeModuleQuiz'])->name('modules.quiz.store');
    Route::get('/modules/{module}/quiz/{quiz}/edit',      [AdminModule::class, 'editModuleQuiz'])->name('modules.quiz.edit');
    Route::put('/modules/{module}/quiz/{quiz}',           [AdminModule::class, 'updateModuleQuiz'])->name('modules.quiz.update');
    Route::delete('/modules/{module}/quiz/{quiz}',        [AdminModule::class, 'destroyModuleQuiz'])->name('modules.quiz.destroy');

    // Établissements, Badges
    Route::resource('etablissements', AdminEtab::class)->except('show');
    Route::resource('badges', AdminBadge::class)->except('show');
    Route::get('/badges/{badge}/holders', [AdminBadge::class, 'holders'])->name('badges.holders');

    // Certificats, Signalements, Paiements
    Route::get('/certificats',                [AdminCert::class, 'index'])->name('certificates.index');
    Route::get('/signalements',               [AdminSignalement::class, 'index'])->name('signalements.index');
    Route::get('/signalements/{signalement}', [AdminSignalement::class, 'show'])->name('signalements.show');
    Route::put('/signalements/{signalement}', [AdminSignalement::class, 'updateStatus'])->name('signalements.update');
    Route::get('/paiements',                  [AdminPayment::class, 'index'])->name('payments.index');

    Route::delete('/resources/{resource}', [AdminModule::class, 'destroyResource'])->name('resources.destroy');

    // Modération forum
    Route::post('/forum/{topic}/lock',               [ForumController::class, 'lock'])->name('forum.lock');
    Route::delete('/forum/{topic}/delete',           [ForumController::class, 'destroy'])->name('forum.delete');
    Route::delete('/forum/message/{message}/delete', [ForumController::class, 'destroyMessage'])->name('forum.message.delete');

    // ✅ CTF — Gestion des challenges (admin)
    Route::resource('ctf', AdminCtf::class)->except('show');
    Route::get('/ctf/{challenge}/stats', [AdminCtf::class, 'stats'])->name('ctf.stats');
});

// ══════════════════════════════════════════════
//  ÉTABLISSEMENT — bloc unique
// ══════════════════════════════════════════════

Route::middleware(['auth', 'role:etablissement'])
    ->prefix('etablissement')
    ->name('etablissement.')
    ->group(function () {

    Route::get('/dashboard', [EtabDash::class, 'index'])->name('dashboard');

    Route::get('/classes',               [EtabDash::class, 'classes'])->name('classes');
    Route::get('/classes/new',           [EtabDash::class, 'createClass'])->name('classes.create');
    Route::post('/classes',              [EtabDash::class, 'storeClass'])->name('classes.store');
    Route::get('/classes/{classe}/edit', [EtabDash::class, 'editClass'])->name('classes.edit');
    Route::put('/classes/{classe}',      [EtabDash::class, 'updateClass'])->name('classes.update');
    Route::delete('/classes/{classe}',   [EtabDash::class, 'destroyClass'])->name('classes.destroy');
    Route::get('/classes/{classe}/stats',[EtabDash::class, 'classStats'])->name('classes.stats');

    Route::get('/eleves',                    [EtabDash::class, 'students'])->name('students');
    Route::get('/eleves/new',                [EtabDash::class, 'createStudent'])->name('students.create');
    Route::post('/eleves',                   [EtabDash::class, 'storeStudent'])->name('students.store');
    Route::get('/eleves/{user}/progression', [EtabDash::class, 'studentProgress'])->name('students.progress');
    Route::get('/eleves/{user}/modifier',    [EtabDash::class, 'editStudent'])->name('students.edit');
    Route::put('/eleves/{user}',             [EtabDash::class, 'updateStudent'])->name('students.update');
    Route::delete('/eleves/{user}',          [EtabDash::class, 'destroyStudent'])->name('students.destroy');

    Route::get('/enseignants',                 [EtabDash::class, 'enseignants'])->name('enseignants');
    Route::get('/enseignants/creer',           [EtabDash::class, 'createEnseignant'])->name('enseignants.create');
    Route::post('/enseignants/creer',          [EtabDash::class, 'storeEnseignant'])->name('enseignants.store');
    Route::get('/enseignants/{user}/modifier', [EtabDash::class, 'editEnseignant'])->name('enseignants.edit');
    Route::put('/enseignants/{user}',          [EtabDash::class, 'updateEnseignant'])->name('enseignants.update');
    Route::delete('/enseignants/{user}',       [EtabDash::class, 'destroyEnseignant'])->name('enseignants.destroy');

    Route::get('/cours',       [EtabDash::class, 'courses'])->name('courses');
    Route::get('/badges',      [EtabDash::class, 'badges'])->name('badges');
    Route::get('/certificats', [EtabDash::class, 'certificates'])->name('certificates');
    Route::get('/paiements',            [EtabPayment::class, 'index'])->name('payments');
    Route::post('/paiements/souscrire', [EtabPayment::class, 'subscribe'])->name('payments.subscribe');

    // ✅ CTF — Stats apprenants de l'établissement
    Route::get('/ctf', [EtabDash::class, 'ctfStats'])->name('ctf');
});

// ══════════════════════════════════════════════
//  ENSEIGNANT
// ══════════════════════════════════════════════

Route::middleware(['auth', 'role:enseignant'])
    ->prefix('enseignant')
    ->name('enseignant.')
    ->group(function () {

    Route::get('/dashboard',                 [EnseignantDash::class, 'index'])->name('dashboard');
    Route::get('/classes',                   [EnseignantDash::class, 'classes'])->name('classes');
    Route::get('/classes/{classe}/stats',    [EnseignantDash::class, 'classStats'])->name('classes.stats');
    Route::get('/classes/{classe}/report',   [EnseignantDash::class, 'classReport'])->name('classes.report');
    Route::get('/eleves',                    [EnseignantDash::class, 'students'])->name('students');
    Route::get('/eleves/{user}/progression', [EnseignantDash::class, 'studentProgress'])->name('students.progress');
});
