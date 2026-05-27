@extends('layouts.app')

@section('title', 'Mes Cours')
@section('page-title', '📚 Mes Cours')

@section('content')

{{-- ✅ Bandeau niveau étudiant --}}
<div class="level-banner">
    <span class="level-banner-icon">
        @if(auth()->user()->getRoleName() === 'eleve') 🏫
        @elseif(auth()->user()->getRoleName() === 'etudiant') 🎓
        @else 🌐
        @endif
    </span>
    <span class="level-banner-text">
        Cours disponibles pour votre niveau :
        <strong>{{ $userLevelLabel }}</strong>
    </span>
</div>

{{-- ✅ Message si aucun module disponible --}}
@if($modules->isEmpty())
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <p class="empty-title">Aucun cours disponible pour l'instant</p>
        <p class="empty-desc">Les modules de votre niveau seront bientôt publiés.</p>
    </div>
@else

<div class="cgrid">

    @foreach($modules as $m)

        <div class="cc">

            {{-- 🎨 Header --}}
            <div class="cth course-header-bg">

                {{-- ✅ Badge niveau coloré selon lycée / université / tous --}}
                <span class="clv {{ match($m->level) {
                    'lycee'      => 'badge-lycee',
                    'universite' => 'badge-universite',
                    default      => 'badge-tous',
                } }}">
                    {{ match($m->level) {
                        'lycee'      => '🏫 Lycée',
                        'universite' => '🎓 Université',
                        default      => '🌐 Tous',
                    } }}
                </span>

                📚
            </div>

            {{-- 📦 Body --}}
            <div class="cbb">

                {{-- 🏷️ Titre --}}
                <div class="cn">{{ $m->title }}</div>

                {{-- 📝 Description --}}
                <div class="cd">{{ Str::limit($m->description, 80) }}</div>

                {{-- 📊 Infos --}}
                <div class="course-meta">
                    <span>📖 {{ $m->lessons_count }} leçon{{ $m->lessons_count > 1 ? 's' : '' }}</span>
                    <span>⏱️ {{ $m->duration_hours }}h</span>
                </div>

                {{-- 📈 Progression --}}
                <div class="course-progress">

                    <div class="course-progress-header">

                        <span class="course-status">
                            @if($m->user_progress >= 100)
                                 Terminé
                            @elseif($m->user_progress > 0)
                                ⏳ En cours
                            @else
                                Non commencé
                            @endif
                        </span>

                        <span class="course-percent {{ $m->user_progress >= 100 ? 'percent-success' : ($m->user_progress > 0 ? 'percent-warning' : '') }}">
                            {{ $m->user_progress }}%
                        </span>

                    </div>

                    <div class="pb">
                        <div class="pf {{ $m->user_progress >= 100 ? 'pfg' : 'pf-gradient' }}"
                             style="width:{{ $m->user_progress }}%">
                        </div>
                    </div>

                </div>

                {{-- 🎯 Action --}}
                @if($m->user_progress >= 100)
                    <a href="{{ route('courses.show', $m) }}" class="bcours course-btn-complete">
                        ✅ Terminé — Revoir
                    </a>
                @elseif($m->user_progress > 0)
                    <a href="{{ route('courses.show', $m) }}" class="bcours">
                        ▶️ Continuer
                    </a>
                @else
                    <a href="{{ route('courses.show', $m) }}" class="bcours st">
                         Commencer
                    </a>
                @endif

            </div>

        </div>

    @endforeach

</div>

@endif



@endsection
