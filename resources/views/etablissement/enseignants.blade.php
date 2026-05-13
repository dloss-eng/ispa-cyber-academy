@extends('layouts.app')

@section('title', 'Enseignants')
@section('page-title', '👨‍🏫 Mes Enseignants')

@section('content')

{{-- ➕ Ajouter --}}
<div class="teachers-header">
    <a href="{{ route('etablissement.enseignants.create') }}"
       class="btn-cyber btn-sm">
        + Ajouter
    </a>
</div>

@forelse($enseignants as $e)

    @php
        $classes = \App\Models\Classe::where('enseignant_id', $e->id)->get();
    @endphp

    <div class="cyber-card teacher-item">

        <div class="teacher-row">

            {{-- 👤 Avatar --}}
            <div class="teacher-avatar">

                @if($e->avatar)
                    <img src="{{ asset('storage/'.$e->avatar) }}" class="teacher-avatar-img">
                @else
                    {{ strtoupper(substr($e->name,0,1)) }}
                @endif

            </div>

            {{-- 📄 Infos --}}
            <div class="teacher-content">

                <div class="teacher-name">
                    {{ $e->name }}
                </div>

                <div class="teacher-email">
                    {{ $e->email }}
                </div>

                {{-- 🏫 Classes --}}
                @if($classes->count() > 0)

                    <div class="teacher-classes">

                        @foreach($classes as $cl)

                            <span class="class-badge">
                                {{ $cl->name }}
                            </span>

                        @endforeach

                    </div>

                @else

                    <div class="teacher-warning">
                        ⚠️ Aucune classe assignée
                    </div>

                @endif

            </div>

            {{-- 🏷️ rôle --}}
            <span class="tag tag-b">Enseignant</span>

            {{-- ✏️ edit --}}
            <a href="{{ route('etablissement.enseignants.edit',$e) }}"
               class="teacher-link link-orange">
                ✏️
            </a>

            {{-- 🗑️ delete --}}
            <form action="{{ route('etablissement.enseignants.destroy',$e) }}"
                  method="POST"
                  class="inline-form"
                  onsubmit="return confirm('Supprimer ?')">

                @csrf
                @method('DELETE')

                <button class="btn-delete">Supprimer</button>

            </form>

        </div>

    </div>

@empty

    <div class="empty-state">
        Aucun enseignant.
    </div>

@endforelse

{{-- pagination --}}
<div class="pagination-box">
    {{ $enseignants->links() }}
</div>

@endsection