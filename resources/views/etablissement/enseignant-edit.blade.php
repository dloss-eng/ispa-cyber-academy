@extends('layouts.app')

@section('title', 'Modifier enseignant')
@section('page-title', ' Modifier enseignant')

@section('content')

{{--  Retour --}}
<a href="{{ route('etablissement.enseignants') }}" class="back-link">
    ← Retour
</a>

{{--  Formulaire --}}
<div class="cyber-card teacher-form-card">

    <form method="POST"
          action="{{ route('etablissement.enseignants.update',$user) }}">

        @csrf
        @method('PUT')

        {{--  Nom --}}
        <label class="fl no-margin-top">Nom</label>
        <input type="text" name="name" value="{{ $user->name }}" required class="fi">

        {{--  Email --}}
        <label class="fl">Email</label>
        <input type="email" name="email" value="{{ $user->email }}" required class="fi">

        {{--  Téléphone --}}
        <label class="fl">Téléphone</label>
        <input type="text" name="phone" value="{{ $user->phone }}" class="fi">

        {{--  Mot de passe --}}
        <div style="border:1px solid var(--bd);border-radius:10px;padding:16px;margin:16px 0;">
            <p style="font-size:12px;color:var(--t2);margin:0 0 12px 0;">
                🔐 Laisser vide pour conserver le mot de passe actuel
            </p>

            <label class="fl no-margin-top">Mot de passe (vide = inchangé)</label>
            <input type="password"
                   name="password"
                   placeholder="Laisser vide pour ne pas changer"
                   class="fi">

            <label class="fl">Confirmer le mot de passe</label>
            <input type="password"
                   name="password_confirmation"
                   placeholder="Répétez le nouveau mot de passe"
                   class="fi">
        </div>

        {{--  Classes --}}
        <label class="fl">Classes assignées</label>

        <div class="class-select-box">

            @php
                $assignedIds = \App\Models\Classe::where('enseignant_id', $user->id)
                    ->pluck('id')
                    ->toArray();
            @endphp

            @foreach(\App\Models\Classe::where('etablissement_id', auth()->user()->etablissement_id)->get() as $c)

                <label class="class-checkbox">

                    <input type="checkbox"
                           name="class_ids[]"
                           value="{{ $c->id }}"
                           {{ in_array($c->id, $assignedIds) ? 'checked' : '' }}
                           class="checkbox-green">

                    {{ $c->name }}

                    <span class="class-level">
                        · {{ $c->level }}
                    </span>

                </label>

            @endforeach

        </div>

        {{-- 🚀 Submit --}}
        <button type="submit" class="btn-lg">
            Modifier
        </button>

    </form>

</div>

@endsection
