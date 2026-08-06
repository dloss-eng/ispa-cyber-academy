@extends('layouts.app')
@section('title', $quiz->title)
@section('page-title', ' Quiz')
@section('content')
<div class="cyber-card" style="padding:0;overflow:hidden">
    <div style="background:linear-gradient(135deg,rgba(255,107,53,0.1),rgba(192,132,252,0.05));padding:20px 24px;border-bottom:1px solid var(--bd)">
        <div style="font-family:'Orbitron',sans-serif;font-size:16px;font-weight:900">{{ $quiz->title }}</div>
        <div style="font-size:12px;color:var(--t2);margin-top:4px">{{ $quiz->lesson->module->title }}</div>
        <div style="display:flex;gap:20px;margin-top:12px;font-size:11px;color:var(--t3)"><span>⏱️ {{ $quiz->time_limit_minutes }} min</span><span>📊 {{ $quiz->passing_score }}%</span><span>🔄 {{ $remaining }} restantes</span></div>
    </div>
    @if($bestAttempt)<div style="padding:12px 24px;background:{{ $bestAttempt->passed ? 'rgba(0,229,160,0.06)' : 'rgba(255,215,0,0.06)' }};border-bottom:1px solid var(--bd);font-size:12px;color:{{ $bestAttempt->passed ? 'var(--gr)' : 'var(--ye)' }}">Meilleur : <strong>{{ $bestAttempt->percentage }}%</strong></div>@endif
    <form action="{{ route('quiz.submit',$quiz) }}" method="POST" id="qF" style="padding:24px">@csrf<input type="hidden" name="time_spent" id="tS" value="0">
        <div style="position:sticky;top:56px;z-index:10;background:rgba(6,12,26,0.95);padding:12px 0;margin-bottom:20px;border-bottom:1px solid var(--bd);display:flex;justify-content:space-between"><span style="font-size:12px;color:var(--t2)">{{ $quiz->questions->count() }} questions</span><div style="font-family:'Orbitron',sans-serif;font-size:16px;font-weight:900" id="tD">{{ sprintf('%02d',$quiz->time_limit_minutes) }}:00</div></div>
        @foreach($quiz->questions as $i => $q)
            <div style="margin-bottom:28px;padding-bottom:24px;border-bottom:1px solid rgba(255,255,255,0.03)">
                <div style="display:flex;gap:12px;margin-bottom:14px"><div style="width:32px;height:32px;border-radius:50%;background:rgba(75,123,255,0.15);color:var(--bl);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:900">{{ $i+1 }}</div><div style="font-size:14px;font-weight:700">{{ $q->question_text }}</div></div>
                <div style="margin-left:44px">@foreach($q->answers as $a)
                    <label class="qopt"><input type="{{ $q->type==='choix_multiple'?'checkbox':'radio' }}" name="answers[{{ $q->id }}]{{ $q->type==='choix_multiple'?'[]':'' }}" value="{{ $a->id }}" style="accent-color:var(--gr);width:16px;height:16px"><span style="font-size:13px">{{ $a->answer_text }}</span></label>
                @endforeach</div>
            </div>
        @endforeach
        <button type="submit" class="btn-cyber" style="width:100%;justify-content:center" onclick="return confirm('Soumettre ?')">📤 Soumettre</button>
    </form>
</div>
@push('scripts')<script>let t={{ $quiz->time_limit_minutes*60 }},e=0;const ti=setInterval(()=>{t--;e++;document.getElementById('tS').value=e;const m=Math.floor(t/60),s=t%60,d=document.getElementById('tD');d.textContent=String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');if(t<=60)d.style.color='var(--re)';if(t<=0){clearInterval(ti);document.getElementById('qF').submit();}},1000);</script>@endpush
@endsection
