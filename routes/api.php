<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\{User, Module, Lesson, Quiz, QuizAttempt, Badge, Certificate, ForumTopic, Etablissement, Signalement};

Route::prefix('v1')->group(function () {
    Route::post('/login', function (Request $r) { $r->validate(['email'=>'required|email','password'=>'required']); $u=User::where('email',$r->email)->first(); if(!$u||!password_verify($r->password,$u->password)) return response()->json(['message'=>'Identifiants incorrects.'],401); return response()->json(['user'=>$u->load('role'),'token'=>$u->createToken('api')->plainTextToken]); });
    Route::get('/modules', fn()=>response()->json(Module::where('is_published',true)->orderBy('order')->get()));
    Route::get('/modules/{module}', fn(Module $m)=>response()->json($m->load('lessons')));
    Route::get('/leaderboard', fn()=>response()->json(User::whereHas('role',fn($q)=>$q->whereIn('name',['eleve','etudiant']))->orderByDesc('points')->take(50)->get(['id','name','points','level','avatar'])));
    Route::get('/badges', fn()=>response()->json(Badge::all()));
    Route::get('/verify-certificate/{code}', function(string $c){$cert=Certificate::with(['user:id,name','module:id,title'])->where('certificate_number',$c)->first();return $cert?response()->json($cert):response()->json(['message'=>'Non trouvé.'],404);});

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', fn(Request $r)=>response()->json($r->user()->load('role','etablissement')));
        Route::post('/logout', function(Request $r){$r->user()->currentAccessToken()->delete();return response()->json(['message'=>'Déconnecté.']);});
        Route::get('/my/stats', function(Request $r){$u=$r->user();return response()->json(['points'=>$u->points,'level'=>$u->level,'badges_count'=>$u->badges()->count(),'certificates_count'=>$u->certificates()->count(),'quizzes_passed'=>QuizAttempt::where('user_id',$u->id)->where('passed',true)->count(),'lessons_completed'=>$u->progress()->where('status','completed')->count()]);});
        Route::get('/my/progress', fn(Request $r)=>response()->json($r->user()->progress()->with('lesson.module')->get()));
        Route::get('/my/badges', fn(Request $r)=>response()->json($r->user()->badges()->get()));
        Route::get('/my/certificates', fn(Request $r)=>response()->json($r->user()->certificates()->with('module')->get()));
        Route::get('/my/notifications', fn(Request $r)=>response()->json(\App\Models\UserNotification::where('user_id',$r->user()->id)->orderByDesc('created_at')->take(50)->get()));
        Route::get('/courses', fn()=>response()->json(Module::where('is_published',true)->withCount('lessons')->orderBy('order')->get()));
        Route::get('/courses/{module}/lessons', fn(Module $m)=>response()->json($m->lessons()->where('is_published',true)->orderBy('order')->get()));
        Route::get('/lessons/{lesson}', fn(Lesson $l)=>response()->json($l->load('resources','quiz.questions.answers')));
        Route::post('/lessons/{lesson}/complete', function(Lesson $l,Request $r){\App\Models\StudentProgress::updateOrCreate(['user_id'=>$r->user()->id,'lesson_id'=>$l->id],['status'=>'completed','progress_percent'=>100,'completed_at'=>now()]);$r->user()->addPoints(10);return response()->json(['message'=>'Leçon complétée. +10 points.']);});
        Route::get('/quiz/{quiz}', fn(Quiz $q)=>response()->json($q->load('questions.answers')));
        Route::post('/quiz/{quiz}/submit', function(Quiz $q,Request $r){$u=$r->user();if($q->remainingAttempts($u)<=0)return response()->json(['message'=>'Épuisé.'],422);$a=$r->input('answers',[]);$s=0;$t=0;foreach($q->questions as $qn){$t+=$qn->points;$c=$qn->correctAnswers->pluck('id')->toArray();$g=$a[$qn->id]??null;if(in_array((int)$g,$c))$s+=$qn->points;}$p=$t>0?round(($s/$t)*100):0;$pa=$p>=$q->passing_score;$at=QuizAttempt::create(['user_id'=>$u->id,'quiz_id'=>$q->id,'score'=>$s,'total_points'=>$t,'percentage'=>$p,'passed'=>$pa,'completed_at'=>now()]);$pts=$pa?25+$p:intval($p/10);$u->addPoints($pts);return response()->json(['attempt'=>$at,'points'=>$pts]);});
        Route::get('/forum', fn()=>response()->json(ForumTopic::with('user:id,name')->withCount('messages')->latest()->take(30)->get()));
        Route::post('/forum', function(Request $r){$r->validate(['title'=>'required','body'=>'required']);return response()->json(ForumTopic::create(['user_id'=>$r->user()->id,'title'=>$r->title,'body'=>$r->body,'module_id'=>$r->module_id]),201);});
        Route::get('/forum/{topic}', fn(ForumTopic $t)=>response()->json($t->load('messages.user:id,name')));
        Route::post('/forum/{topic}/reply', function(ForumTopic $t,Request $r){$r->validate(['body'=>'required']);return response()->json($t->messages()->create(['user_id'=>$r->user()->id,'body'=>$r->body]),201);});
        Route::middleware('role:admin')->prefix('admin')->group(function(){
            Route::get('/stats', fn()=>response()->json(['total_users'=>User::count(),'total_modules'=>Module::count(),'total_certificates'=>Certificate::count(),'total_signalements'=>Signalement::count()]));
            Route::get('/users', fn()=>response()->json(User::with('role')->paginate(20)));
            Route::get('/etablissements', fn()=>response()->json(Etablissement::withCount('users')->get()));
        });
    });
});
