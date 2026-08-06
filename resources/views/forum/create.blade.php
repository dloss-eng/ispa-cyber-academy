@extends('layouts.app')

@section('title', 'Nouveau sujet')
@section('page-title', ' Nouveau sujet')

@section('content')

<div class="cyber-card forum-form">

    <form method="POST" action="{{ route('forum.store') }}">
        @csrf

        {{-- Titre --}}
        <label class="fl no-margin-top">Titre</label>
        <input type="text" name="title" required class="fi">

        {{-- Module --}}
        <label class="fl">Module</label>
        <select name="module_id" class="fi">
            <option value="">Général</option>
            @foreach($modules as $m)
                <option value="{{ $m->id }}">{{ $m->title }}</option>
            @endforeach
        </select>

        {{-- Message --}}
        <label class="fl">Message</label>
        <textarea name="body" rows="6" required class="fi"></textarea>

        {{-- Submit --}}
        <button type="submit" class="btn-lg">
            Publier
        </button>

    </form>

</div>

@endsection