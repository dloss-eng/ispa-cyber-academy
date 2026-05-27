@extends('layouts.course')
@section('title', $module->title)

@section('content')

<div class="course-layout">

    {{-- ═══════ SIDEBAR GAUCHE ═══════ --}}
    <aside class="course-sidebar" id="courseSidebar">

        <div class="cs-header">
            <a href="{{ route('courses.index') }}" class="cs-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Mes cours
            </a>
            <div class="cs-module-name">{{ $module->title }}</div>
            <div class="cs-progress-wrap">
                <div class="cs-progress-bar">
                    <div class="cs-progress-fill {{ $moduleProgress >= 100 ? 'cs-pf-success' : '' }}"
                         style="width:{{ $moduleProgress }}%" id="progressFill"></div>
                </div>
                <span class="cs-progress-pct {{ $moduleProgress >= 100 ? 'text-gr' : 'text-or' }}" id="progressPct">
                    {{ $moduleProgress }}%
                </span>
            </div>
            <div class="cs-stats">
                <span>{{ $lessonsWithProgress->count() }} leçons</span>
                <span id="completedCount">{{ $lessonsWithProgress->where('user_status','completed')->count() }} terminées</span>
            </div>
        </div>

        <ul class="cs-lesson-list">
            @foreach($lessonsWithProgress as $i => $l)
                <li class="cs-lesson-item {{ $l->user_status }}" id="sb-{{ $l->id }}">
                    <a href="#" class="cs-lesson-link"
                       onclick="loadLesson('{{ route('courses.lesson.ajax', [$module, $l]) }}', {{ $l->id }}); return false;">
                        <span class="cs-lesson-num {{ $l->user_status }}" id="num-{{ $l->id }}">
                            @if($l->user_status === 'completed')
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </span>
                        <div class="cs-lesson-info">
                            <div class="cs-lesson-title">{{ $l->title }}</div>
                            <div class="cs-lesson-meta">
                                {{ $l->duration_minutes }} min
                                @if($l->quizzes->first()) <span class="cs-quiz-dot">· Quiz</span> @endif
                            </div>
                        </div>
                        <span class="cs-status-dot {{ $l->user_status }}" id="dot-{{ $l->id }}"></span>
                    </a>
                </li>
            @endforeach
        </ul>

    </aside>

    {{-- ═══════ CONTENU PRINCIPAL ═══════ --}}
    <main class="course-main" id="courseMain">

        <button class="cs-toggle" onclick="toggleCourseSidebar()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            Plan du cours
        </button>

        {{-- ═══ VUE PAR DÉFAUT ═══ --}}
        <div id="moduleOverview">

            {{-- ── Hero ── --}}
            <div class="mo-hero">
                <div class="mo-hero-bg"></div>
                <div class="mo-hero-content">

                    <span class="mo-level-badge {{ match($module->level) {
                        'lycee'      => 'badge-lycee',
                        'universite' => 'badge-universite',
                        default      => 'badge-tous',
                    } }}">{{ match($module->level) {
                        'lycee'      => '🏫 Lycée',
                        'universite' => '🎓 Université',
                        default      => '🌐 Tous niveaux',
                    } }}</span>

                    <h1 class="mo-title">{{ $module->title }}</h1>
                    <p class="mo-desc">{{ $module->description }}</p>

                    {{-- Stats --}}
                    <div class="mo-stats-row">
                        <div class="mo-stat">
                            <span class="mo-stat-val">{{ $lessonsWithProgress->count() }}</span>
                            <span class="mo-stat-lbl">Leçons</span>
                        </div>
                        <div class="mo-stat-sep"></div>
                        <div class="mo-stat">
                            <span class="mo-stat-val">{{ $module->duration_hours }}h</span>
                            <span class="mo-stat-lbl">Durée</span>
                        </div>
                        <div class="mo-stat-sep"></div>
                        <div class="mo-stat">
                            <span class="mo-stat-val">{{ $lessonsWithProgress->where('user_status','completed')->count() }}</span>
                            <span class="mo-stat-lbl">Terminées</span>
                        </div>
                        <div class="mo-stat-sep"></div>
                        <div class="mo-stat">
                            <span class="mo-stat-val" style="color:{{ $moduleProgress >= 100 ? 'var(--gr)' : 'var(--or)' }}">{{ $moduleProgress }}%</span>
                            <span class="mo-stat-lbl">Progression</span>
                        </div>
                    </div>

                    <div class="mo-progress-bar">
                        <div class="mo-progress-fill {{ $moduleProgress >= 100 ? 'mo-pf-done' : '' }}"
                             style="width:{{ $moduleProgress }}%"></div>
                    </div>

                    @php
                        $firstPending = $lessonsWithProgress->firstWhere('user_status', '!=', 'completed');
                        $targetLesson = $firstPending ?? $lessonsWithProgress->first();
                    @endphp
                    @if($targetLesson)
                        <button class="mo-cta-btn"
                            onclick="loadLesson('{{ route('courses.lesson.ajax', [$module, $targetLesson]) }}', {{ $targetLesson->id }})">
                            @if($moduleProgress >= 100) ↺ Revoir depuis le début
                            @elseif($moduleProgress > 0) ▶ Continuer — {{ Str::limit($targetLesson->title, 30) }}
                            @else 🚀 Commencer le module
                            @endif
                        </button>
                    @endif
                </div>
            </div>

            {{-- ── Section titre avec toggle  ── --}}
            <div class="mo-section-title mo-section-toggle" onclick="toggleLessons()">
                <div style="display:flex;align-items:center;gap:8px">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                    Contenu du module
                    <span class="mo-lesson-count">{{ $lessonsWithProgress->count() }} leçon{{ $lessonsWithProgress->count() > 1 ? 's' : '' }}</span>
                </div>
                <svg id="toggleIcon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                     style="transition:transform .25s;flex-shrink:0">
                    <polyline points="18 15 12 9 6 15"/>
                </svg>
            </div>

            {{-- ── Liste leçons ── --}}
            <div class="cm-lessons" id="lessonsContainer">
                @foreach($lessonsWithProgress as $i => $l)
                    <a href="#" class="cm-lesson-link"
                       onclick="loadLesson('{{ route('courses.lesson.ajax', [$module, $l]) }}', {{ $l->id }}); return false;">
                        <div class="cm-lesson-card {{ $l->user_status }}">
                            <div class="cm-lesson-num {{ $l->user_status }}">
                                @if($l->user_status === 'completed')
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                @else {{ $i + 1 }} @endif
                            </div>
                            <div class="cm-lesson-body">
                                <div class="cm-lesson-title">{{ $l->title }}</div>
                                <div class="cm-lesson-meta">
                                    <span>⏱ {{ $l->duration_minutes }} min</span>
                                    @if($l->quizzes->first()) <span class="cm-quiz-badge">📝 Quiz</span> @endif
                                    @if(isset($l->resources) && $l->resources->count() > 0)
                                        <span class="cm-res-badge">📎 {{ $l->resources->count() }} ressource{{ $l->resources->count() > 1 ? 's' : '' }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="cm-status-tag {{ $l->user_status }}">
                                @if($l->user_status === 'completed')  Terminé
                                @elseif($l->user_status === 'in_progress') ⏳ En cours
                                @else À faire @endif
                            </span>
                            <svg class="cm-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </div>
                    </a>
                @endforeach
            </div>

        </div>

        {{-- ═══ PANNEAU LEÇON ═══ --}}
        <div id="lessonPanel" style="display:none;">
            <button class="lp-back-btn" onclick="backToOverview()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                Retour au cours
            </button>
            <div id="lessonLoader" style="display:none;">
                <div class="lp-loader"><div class="lp-spinner"></div><span>Chargement...</span></div>
            </div>
            <div id="lessonBody"></div>
        </div>

    </main>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let lessonsOpen = false;

// ── Toggle Contenu du module  ────────────────────────────────
function toggleLessons() {
    const container = document.getElementById('lessonsContainer');
    const icon      = document.getElementById('toggleIcon');
    lessonsOpen = !lessonsOpen;

    if (lessonsOpen) {
        container.style.maxHeight = container.scrollHeight + 'px';
        container.style.opacity   = '1';
        icon.style.transform      = 'rotate(0deg)';
        setTimeout(() => { container.style.maxHeight = 'none'; }, 320);
    } else {
        container.style.maxHeight = container.scrollHeight + 'px';
        requestAnimationFrame(() => {
            container.style.maxHeight = '0';
            container.style.opacity   = '0';
        });
        icon.style.transform = 'rotate(180deg)';
    }
}

// Init transition au chargement
window.addEventListener('DOMContentLoaded', () => {
    const c = document.getElementById('lessonsContainer');
    const icon = document.getElementById('toggleIcon');
    if (c) {
        c.style.overflow   = 'hidden';
        c.style.transition = 'max-height .3s ease, opacity .25s ease';
        //  Fermé par défaut
        c.style.maxHeight  = '0';
        c.style.opacity    = '0';
        if (icon) icon.style.transform = 'rotate(180deg)';
    }
});

// ── Sidebar mobile ─────────────────────────────────────────────
function toggleCourseSidebar() {
    document.getElementById('courseSidebar').classList.toggle('open');
}

// ── Charger une leçon ──────────────────────────────────────────
function loadLesson(ajaxUrl, lessonId) {
    document.querySelectorAll('.cs-lesson-item').forEach(i => i.classList.remove('active-lesson'));
    const sbItem = document.getElementById('sb-' + lessonId);
    if (sbItem) sbItem.classList.add('active-lesson');

    document.getElementById('moduleOverview').style.display = 'none';
    document.getElementById('lessonPanel').style.display = 'block';
    document.getElementById('lessonLoader').style.display = 'flex';
    document.getElementById('lessonBody').innerHTML = '';
    document.getElementById('courseMain').scrollTo({ top: 0, behavior: 'smooth' });

    fetch(ajaxUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF } })
    .then(res => { if (!res.ok) throw new Error('HTTP ' + res.status); return res.text(); })
    .then(html => {
        document.getElementById('lessonLoader').style.display = 'none';
        document.getElementById('lessonBody').innerHTML = html;

        const form = document.getElementById('completeForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = form.querySelector('button[type=submit]');
                if (btn) { btn.disabled = true; btn.textContent = '⏳ En cours...'; }

                fetch(form.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF }, body: new FormData(form) })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    const dot  = document.getElementById('dot-' + lessonId);
                    const num  = document.getElementById('num-' + lessonId);
                    const item = document.getElementById('sb-' + lessonId);
                    if (dot)  dot.className = 'cs-status-dot completed';
                    if (num)  { num.className = 'cs-lesson-num completed'; num.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>'; }
                    if (item) { item.classList.remove('active-lesson'); item.classList.add('completed'); setTimeout(()=>item.classList.add('active-lesson'),10); }
                    if (data.moduleProgress !== undefined) {
                        const fill = document.getElementById('progressFill');
                        const pct  = document.getElementById('progressPct');
                        if (fill) fill.style.width = data.moduleProgress + '%';
                        if (pct)  pct.textContent  = data.moduleProgress + '%';
                    }
                    loadLesson(ajaxUrl, lessonId);
                })
                .catch(() => window.location.reload());
            });
        }
    })
    .catch(() => {
        document.getElementById('lessonLoader').style.display = 'none';
        document.getElementById('lessonBody').innerHTML = '<div style="color:var(--or);padding:20px;background:rgba(255,107,53,.08);border-radius:10px;border:1px solid rgba(255,107,53,.2);">⚠️ Erreur de chargement. <button onclick="loadLesson(\'' + ajaxUrl + '\',' + lessonId + ')" style="background:none;border:none;color:var(--bl);cursor:pointer;text-decoration:underline;margin-left:8px;">Réessayer</button></div>';
    });
}

function backToOverview() {
    document.getElementById('lessonPanel').style.display = 'none';
    document.getElementById('moduleOverview').style.display = 'block';
    document.querySelectorAll('.cs-lesson-item').forEach(i => i.classList.remove('active-lesson'));
    document.getElementById('courseMain').scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

@endsection
