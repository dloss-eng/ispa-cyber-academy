@extends('layouts.app')
@section('title', 'Épuisé')
@section('page-title', '⚠️ Tentatives épuisées')
@section('content')
<div style="text-align:center;padding:60px 20px"><div style="font-size:56px;margin-bottom:16px">⚠️</div><div style="font-family:'Orbitron',sans-serif;font-size:20px;font-weight:900;margin-bottom:12px">Tentatives épuisées</div>@if($bestAttempt)<div style="color:var(--t2)">Meilleur : <span style="font-family:'Orbitron',sans-serif;font-weight:900;color:{{ $bestAttempt->passed?'var(--gr)':'var(--re)' }}">{{ $bestAttempt->percentage }}%</span></div>@endif<a href="{{ route('courses.show',$quiz->lesson->module) }}" class="btn-cyber btn-sm" style="margin-top:24px">← Module</a></div>
@endsection
