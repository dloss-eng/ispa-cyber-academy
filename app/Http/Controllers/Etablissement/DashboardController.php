<?php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\{User, Classe, StudentProgress, Role, Etablissement, QuizAttempt, Certificate, Module, Challenge, ChallengeAttempt};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, DB};

class DashboardController extends Controller
{
    // ============================
    // DASHBOARD
    // ============================

    public function index()
    {
        // ✅ Sécurité : vérifier que l'établissement existe
        $etab = Etablissement::find(Auth::user()->etablissement_id);
        abort_if(!$etab, 403, 'Aucun établissement associé à ce compte. Contactez un administrateur.');

        $studentCount = User::where('etablissement_id', $etab->id)
            ->whereHas('role', fn($q) => $q->whereIn('name', ['eleve', 'etudiant']))
            ->count();

        $enseignantCount = User::where('etablissement_id', $etab->id)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->count();

        $classes = Classe::where('etablissement_id', $etab->id)
            ->withCount('students')
            ->get();

        $recentProgress = StudentProgress::whereHas('user', fn($q) =>
                $q->where('etablissement_id', $etab->id)
            )
            ->with(['user', 'lesson'])
            ->latest()
            ->limit(10)
            ->get();

        return view('etablissement.dashboard', compact(
            'etab', 'studentCount', 'enseignantCount', 'classes', 'recentProgress'
        ));
    }

    // ============================
    // CLASSES
    // ============================

    public function classes()
    {
        $classes = Classe::where('etablissement_id', Auth::user()->etablissement_id)
            ->withCount('students')
            ->get();

        return view('etablissement.classes', compact('classes'));
    }

    public function createClass()
    {
        $etab = Etablissement::find(Auth::user()->etablissement_id);
        abort_if(! $etab, 403);
        return view('etablissement.class-form', compact('etab'));
    }

    public function storeClass(Request $r)
    {
        $data = $r->validate([
            'name'  => 'required|string|max:255',
            'level' => 'required|string|max:100',
            'year'  => 'nullable|string|max:20',
        ]);

        Classe::create([
            ...$data,
            'etablissement_id' => Auth::user()->etablissement_id,
        ]);

        return redirect()->route('etablissement.classes')->with('success', 'Classe créée.');
    }

    public function editClass(Classe $classe)
    {
        abort_if($classe->etablissement_id !== Auth::user()->etablissement_id, 403);
        return view('etablissement.class-form', compact('classe'));
    }

    public function updateClass(Request $r, Classe $classe)
    {
        abort_if($classe->etablissement_id !== Auth::user()->etablissement_id, 403);

        $data = $r->validate([
            'name'  => 'required|string|max:255',
            'level' => 'required|string|max:100',
            'year'  => 'nullable|string|max:20',
        ]);

        $classe->update($data);
        return redirect()->route('etablissement.classes')->with('success', 'Classe modifiée.');
    }

    public function destroyClass(Classe $classe)
    {
        abort_if($classe->etablissement_id !== Auth::user()->etablissement_id, 403);
        $classe->delete();
        return redirect()->route('etablissement.classes')->with('success', 'Supprimée.');
    }

    public function classStats(Classe $classe)
    {
        abort_if($classe->etablissement_id !== Auth::user()->etablissement_id, 403);

        $students = $classe->students()->with('badges')->get();
        $ids      = $students->pluck('id');

        $progressCounts = StudentProgress::whereIn('user_id', $ids)
            ->where('status', 'completed')
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $quizCounts = QuizAttempt::whereIn('user_id', $ids)
            ->where('passed', true)
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $stats = $students->map(fn($s) => [
            'user'     => $s,
            'progress' => $progressCounts[$s->id] ?? 0,
            'quizzes'  => $quizCounts[$s->id] ?? 0,
            'points'   => $s->points,
        ]);

        return view('etablissement.class-stats', compact('classe', 'stats'));
    }

    // ============================
    // ÉTUDIANTS
    // ============================

    public function students()
    {
        $students = User::where('etablissement_id', Auth::user()->etablissement_id)
            ->whereHas('role', fn($q) => $q->whereIn('name', ['eleve', 'etudiant']))
            ->paginate(20);

        return view('etablissement.students', compact('students'));
    }

    public function createStudent()
    {
        $etab    = Etablissement::find(Auth::user()->etablissement_id);
        $classes = Classe::where('etablissement_id', Auth::user()->etablissement_id)->get();
        return view('etablissement.student-form', compact('classes', 'etab'));
    }

    public function storeStudent(Request $r)
    {
        $data = $r->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $role = Role::where('name', 'etudiant')->firstOrFail();

        DB::transaction(function () use ($data, $role) {
            $user = User::create([
                'name'             => $data['name'],
                'email'            => $data['email'],
                'password'         => Hash::make($data['password']),
                'role_id'          => $role->id,
                'etablissement_id' => Auth::user()->etablissement_id,
            ]);

            if (!empty($data['class_id'])) {
                $user->classes()->attach($data['class_id']);
            }
        });

        return redirect()->route('etablissement.students')->with('success', 'Élève créé.');
    }

    public function editStudent(User $user)
    {
        abort_if($user->etablissement_id !== Auth::user()->etablissement_id, 403);
        $classes = Classe::where('etablissement_id', Auth::user()->etablissement_id)->get();
        return view('etablissement.student-edit', compact('user', 'classes'));
    }

    public function updateStudent(Request $r, User $user)
    {
        abort_if($user->etablissement_id !== Auth::user()->etablissement_id, 403);

        $data = $r->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if (isset($data['class_id'])) {
            $user->classes()->sync([$data['class_id']]);
        }

        return redirect()->route('etablissement.students')->with('success', 'Élève modifié.');
    }

    public function destroyStudent(User $user)
    {
        abort_if($user->etablissement_id !== Auth::user()->etablissement_id, 403);

        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'Impossible de vous supprimer.']);
        }

        $user->delete();
        return redirect()->route('etablissement.students')->with('success', 'Supprimé.');
    }

    public function studentProgress(User $user)
    {
        abort_if($user->etablissement_id !== Auth::user()->etablissement_id, 403);

        $progress = StudentProgress::where('user_id', $user->id)
            ->with('lesson.module')
            ->get();

        $attempts = QuizAttempt::where('user_id', $user->id)
            ->with('quiz')
            ->latest()
            ->get();

        return view('etablissement.student-progress', compact('user', 'progress', 'attempts'));
    }

    // ============================
    // ENSEIGNANTS
    // ============================

    public function enseignants()
    {
        $enseignants = User::where('etablissement_id', Auth::user()->etablissement_id)
            ->whereHas('role', fn($q) => $q->where('name', 'enseignant'))
            ->paginate(20);

        return view('etablissement.enseignants', compact('enseignants'));
    }

    public function createEnseignant()
    {
        $classes = Classe::where('etablissement_id', Auth::user()->etablissement_id)->get();
        return view('etablissement.enseignant-form', compact('classes'));
    }

    public function storeEnseignant(Request $r)
    {
        $data = $r->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $role = Role::where('name', 'enseignant')->firstOrFail();

        DB::transaction(function () use ($data, $role, $r) {
            $user = User::create([
                'name'             => $data['name'],
                'email'            => $data['email'],
                'password'         => Hash::make($data['password']),
                'role_id'          => $role->id,
                'etablissement_id' => Auth::user()->etablissement_id,
            ]);

            if ($r->has('class_ids')) {
                Classe::whereIn('id', $r->class_ids)
                    ->where('etablissement_id', Auth::user()->etablissement_id)
                    ->update(['enseignant_id' => $user->id]);
            }
        });

        return redirect()->route('etablissement.enseignants')->with('success', 'Enseignant créé.');
    }

    public function editEnseignant(User $user)
    {
        abort_if($user->etablissement_id !== Auth::user()->etablissement_id, 403);
        $classes = Classe::where('etablissement_id', Auth::user()->etablissement_id)->get();
        return view('etablissement.enseignant-edit', compact('user', 'classes'));
    }

    public function updateEnseignant(Request $r, User $user)
    {
        abort_if($user->etablissement_id !== Auth::user()->etablissement_id, 403);

        $data = $r->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if ($r->has('class_ids')) {
            Classe::whereIn('id', $r->class_ids)
                ->where('etablissement_id', Auth::user()->etablissement_id)
                ->update(['enseignant_id' => $user->id]);
        }

        return redirect()->route('etablissement.enseignants')->with('success', 'Enseignant modifié.');
    }

    public function destroyEnseignant(User $user)
    {
        abort_if($user->etablissement_id !== Auth::user()->etablissement_id, 403);
        $user->delete();
        return redirect()->route('etablissement.enseignants')->with('success', 'Supprimé.');
    }

    // ============================
    // BADGES & CERTIFICATS
    // ============================

    public function badges()
    {
        $students = User::where('etablissement_id', Auth::user()->etablissement_id)
            ->whereHas('role', fn($q) => $q->whereIn('name', ['eleve', 'etudiant']))
            ->with('badges')
            ->get();

        return view('etablissement.badges', compact('students'));
    }

    public function certificates()
    {
        $certificates = Certificate::whereHas('user', fn($q) =>
                $q->where('etablissement_id', Auth::user()->etablissement_id)
            )
            ->with(['user', 'module'])
            ->latest()
            ->get();

        return view('etablissement.certificates', compact('certificates'));
    }

    // ============================
    // COURS
    // ============================

    public function courses()
    {
        $modules = Module::where('is_published', true)
            ->withCount('lessons')
            ->orderBy('order')
            ->get();

        return view('etablissement.courses', compact('modules'));
    }

    // ============================
    // CTF — STATS APPRENANTS ✅
    // ============================

    public function ctfStats()
    {
        $etab = Etablissement::find(Auth::user()->etablissement_id);
        abort_if(! $etab, 403, 'Aucun établissement associé.');

        // Tous les apprenants de cet établissement
        $studentIds = User::where('etablissement_id', $etab->id)
            ->whereHas('role', fn($q) => $q->whereIn('name', ['eleve', 'etudiant']))
            ->pluck('id');

        // Challenges publiés
        $challenges = Challenge::where('is_published', true)
            ->orderBy('difficulty')
            ->orderBy('order')
            ->get();

        // Score CTF par étudiant
        $scores = ChallengeAttempt::whereIn('user_id', $studentIds)
            ->where('is_correct', true)
            ->selectRaw('user_id, SUM(points_earned) as total_points, COUNT(DISTINCT challenge_id) as solved_count')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->with('user:id,name')
            ->get();

        // Taux de résolution par challenge (filtré à l'établissement)
        $challengeStats = $challenges->map(function ($c) use ($studentIds) {
            $solvers = ChallengeAttempt::where('challenge_id', $c->id)
                ->whereIn('user_id', $studentIds)
                ->where('is_correct', true)
                ->distinct('user_id')
                ->count('user_id');

            $attempts = ChallengeAttempt::where('challenge_id', $c->id)
                ->whereIn('user_id', $studentIds)
                ->count();

            return [
                'challenge'    => $c,
                'solvers'      => $solvers,
                'attempts'     => $attempts,
                'success_rate' => $attempts > 0 ? round(($solvers / $attempts) * 100) : 0,
            ];
        });

        // Totaux globaux
        $totalSolved    = ChallengeAttempt::whereIn('user_id', $studentIds)->where('is_correct', true)->count();
        $totalAttempts  = ChallengeAttempt::whereIn('user_id', $studentIds)->count();
        $activeStudents = ChallengeAttempt::whereIn('user_id', $studentIds)
            ->distinct('user_id')->count('user_id');

        return view('etablissement.ctf', compact(
            'etab', 'scores', 'challenges', 'challengeStats',
            'totalSolved', 'totalAttempts', 'activeStudents', 'studentIds'
        ));
    }
}
