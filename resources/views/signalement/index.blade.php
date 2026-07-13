@extends('layouts.app')
@section('title', 'Signalements')
@section('page-title', '🚨 Mes Signalements')
@section('content')
<div style="display:flex;justify-content:flex-end;margin-bottom:16px"><a href="{{ route('signalements.create') }}" class="btn-cyber btn-sm"> Signaler une arnaque</a></div>
@foreach($signalements as $s)
    <div class="cyber-card" style="padding:14px 18px;margin-bottom:8px;display:flex;align-items:center;gap:14px">
        <div style="font-size:24px">{{ explode(' ',$s->type_label)[0] }}</div>
        <div style="flex:1"><div style="font-size:13px;font-weight:700">{{ $s->type_label }}</div><div style="font-size:11px;color:var(--t2)">{{ Str::limit($s->description,60) }}</div><div style="font-size:9px;color:var(--t3)">N° {{ $s->ticket_number }} · {{ $s->created_at->diffForHumans() }}</div></div>
        <span class="tag {{ $s->status==='traite'?'tag-g':($s->status==='en_cours'?'tag-y':'tag-o') }}">{{ ucfirst($s->status) }}</span>
        @if($s->ai_category)<span style="font-size:10px;color:var(--bl)">IA: {{ $s->ai_confidence }}%</span>@endif
    </div>
@endforeach
<div style="margin-top:16px">{{ $signalements->links() }}</div>
@endsection
