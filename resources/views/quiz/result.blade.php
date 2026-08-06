@extends('layouts.app')
@section('title', 'Résultat')
@section('page-title', ' Résultat')
@section('content')
<div class="cyber-card" style="padding:32px;text-align:center;margin-bottom:20px;background:{{ $attempt->passed ? 'linear-gradient(135deg,rgba(0,229,160,0.08),rgba(0,229,160,0.02))' : 'linear-gradient(135deg,rgba(255,59,92,0.08),rgba(255,59,92,0.02))' }}">
    <div style="font-size:56px">{{ $attempt->passed ? '🎉' : '😔' }}</div>
    <div style="font-family:'Orbitron',sans-serif;font-size:24px;font-weight:900;color:{{ $attempt->passed ? 'var(--gr)' : 'var(--re)' }};margin-top:12px">{{ $attempt->passed ? 'Réussi !' : 'Non réussi' }}</div>
    <div style="font-family:'Orbitron',sans-serif;font-size:48px;font-weight:900;color:{{ $attempt->passed ? 'var(--gr)' : 'var(--re)' }};margin:12px 0">{{ $attempt->percentage }}%</div>
    <div style="font-size:13px;color:var(--t2)">{{ $attempt->score }}/{{ $attempt->total_points }} pts</div>
    @if($pointsEarned>0)<div style="display:inline-block;margin-top:14px;background:rgba(255,215,0,0.1);border:1px solid rgba(255,215,0,0.3);border-radius:20px;padding:8px 20px;font-size:13px;font-weight:700;color:var(--ye)">+{{ $pointsEarned }} XP ⭐</div>@endif
</div>
@foreach($quiz->questions as $i => $q)@php $r=$answersData[$q->id]??null; @endphp
    <div class="cyber-card" style="padding:14px;margin-bottom:8px;border-color:{{ ($r['correct']??false) ? 'rgba(0,229,160,0.3)' : 'rgba(255,59,92,0.3)' }}">
        <div style="display:flex;gap:8px;margin-bottom:8px"><div style="width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:900;{{ ($r['correct']??false)?'background:rgba(0,229,160,0.15);color:var(--gr)':'background:rgba(255,59,92,0.15);color:var(--re)' }}">{{ ($r['correct']??false)?'✓':'✗' }}</div><div style="font-size:13px;font-weight:700">{{ $q->question_text }}</div></div>
        <div style="margin-left:32px">@foreach($q->answers as $a)<div style="font-size:12px;padding:3px 0;{{ $a->is_correct?'color:var(--gr);font-weight:600':'' }}{{ !$a->is_correct&&$a->id==($r['user_answer']??null)?';color:var(--re);text-decoration:line-through':'' }}">{{ $a->is_correct?'✅':($a->id==($r['user_answer']??null)?'❌':'○') }} {{ $a->answer_text }}</div>@endforeach</div>
        @if($r['explanation']??null)<div style="margin:8px 0 0 32px;padding:8px;background:rgba(75,123,255,0.06);border-radius:6px;font-size:11px;color:var(--bl)">💡 {{ $r['explanation'] }}</div>@endif
    </div>
@endforeach
<div style="display:flex;gap:12px;margin-top:20px"><a href="{{ route('courses.show',$quiz->lesson->module) }}" class="btn-cyber btn-sm">← Module</a>@if(!$attempt->passed&&$quiz->remainingAttempts(auth()->user())>0)<a href="{{ route('quiz.show',$quiz) }}" class="btn-cyber-outline btn-sm">🔄 Réessayer</a>@endif</div>
@endsection
