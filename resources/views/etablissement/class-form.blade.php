@extends('layouts.app')

@section('title', isset($classe) ? 'Modifier' : 'Nouvelle classe')
@section('page-title', isset($classe) ? '✏️ Modifier' : '➕ Nouvelle classe')

@section('content')

{{-- 🔙 Retour --}}
<a href="{{ route('etablissement.classes') }}" class="back-link">
    ← Retour
</a>

{{-- 📦 Carte --}}
<div class="cyber-card class-form-card">

    <form method="POST"
          action="{{ isset($classe) ? route('etablissement.classes.update',$classe) : route('etablissement.classes.store') }}">

        @csrf
        @if(isset($classe))
            @method('PUT')
        @endif

        {{-- 🏷️ Nom --}}
        <label class="fl no-margin-top">Nom de la classe</label>
        <input type="text"
               name="name"
               value="{{ old('name',$classe->name??'') }}"
               required
               class="fi">

        {{-- 🎓 Niveau --}}
        <label class="fl">Niveau</label>

        <select name="level" required class="fi">

            @if($etab->type === 'lycee')

                @foreach(['6ème','5ème','4ème','3ème','Seconde','Première','Terminale'] as $n)

                    <option value="{{ Str::slug($n) }}"
                        {{ old('level',$classe->level??'') == Str::slug($n) ? 'selected' : '' }}>
                        {{ $n }}
                    </option>

                @endforeach

            @else

                @foreach(['Licence 1','Licence 2','Licence 3','Master 1','Master 2','BTS 1','BTS 2'] as $n)

                    <option value="{{ Str::slug($n) }}"
                        {{ old('level',$classe->level??'') == Str::slug($n) ? 'selected' : '' }}>
                        {{ $n }}
                    </option>

                @endforeach

            @endif

        </select>

        {{-- 📅 Année --}}
        <label class="fl">Année scolaire</label>

        <input type="text"
               name="year"
               value="{{ old('year',$classe->year??date('Y').'-'.(date('Y')+1)) }}"
               class="fi">

        {{-- 🚀 Submit --}}
        <button type="submit" class="btn-lg">
            {{ isset($classe) ? 'Modifier' : 'Créer' }}
        </button>

    </form>

</div>

@endsection