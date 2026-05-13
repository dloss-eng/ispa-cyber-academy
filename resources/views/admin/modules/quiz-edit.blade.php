@extends('layouts.app')

@section('title', 'Modifier Quiz')
@section('page-title', '✏️ Modifier Quiz')

@section('content')

<div class="quiz-lesson-info">
    Leçon : <strong class="quiz-lesson-title">{{ $lesson->title }}</strong>
</div>

<form method="POST" action="{{ route('admin.lessons.quiz.update', $quiz) }}">
    @csrf
    @method('PUT')

    {{-- Paramètres du quiz --}}
    <div class="cyber-card quiz-card">
        <div class="form-grid-2">
            <div>
                <label class="fl no-margin-top">Titre</label>
                <input type="text" name="title" value="{{ $quiz->title }}" required class="fi">
            </div>
            <div>
                <label class="fl no-margin-top">Score requis (%)</label>
                <input type="number" name="passing_score" value="{{ $quiz->passing_score }}" class="fi">
            </div>
        </div>
        <div class="form-grid-2">
            <div>
                <label class="fl">Temps (min)</label>
                <input type="number" name="time_limit_minutes" value="{{ $quiz->time_limit_minutes }}" class="fi">
            </div>
            <div>
                <label class="fl">Max tentatives</label>
                <input type="number" name="max_attempts" value="{{ $quiz->max_attempts }}" class="fi">
            </div>
        </div>
        <label class="checkbox-inline">
            <input type="checkbox" name="is_published" value="1"
                   {{ $quiz->is_published ? 'checked' : '' }}
                   class="checkbox-input">
            Publier
        </label>
    </div>

    {{-- Questions existantes --}}
    <div class="section-title section-blue">📝 QUESTIONS EXISTANTES</div>

    @foreach($quiz->questions as $q)
        <div class="cyber-card question-card">
            <div class="question-header">Question {{ $loop->iteration }}</div>

            <label class="fl no-margin-top">Texte</label>
            <textarea name="existing_questions[{{ $q->id }}][question_text]" rows="2" required class="fi">{{ $q->question_text }}</textarea>

            <div class="form-grid-2">
                <div>
                    <label class="fl">Type</label>
                    <select name="existing_questions[{{ $q->id }}][type]" class="fi">
                        <option value="qcm" {{ $q->type==='qcm'?'selected':'' }}>QCM</option>
                        <option value="vrai_faux" {{ $q->type==='vrai_faux'?'selected':'' }}>Vrai/Faux</option>
                        <option value="choix_multiple" {{ $q->type==='choix_multiple'?'selected':'' }}>Multiple</option>
                    </select>
                </div>
                <div>
                    <label class="fl">Points</label>
                    <input type="number" name="existing_questions[{{ $q->id }}][points]" value="{{ $q->points }}" class="fi">
                </div>
            </div>

            <label class="fl">Explication</label>
            <input type="text" name="existing_questions[{{ $q->id }}][explanation]" value="{{ $q->explanation }}" class="fi">

            <label class="fl">Réponses</label>
            @foreach($q->answers as $a)
                <div class="answer-row">
                    <input type="text"
                           name="existing_questions[{{ $q->id }}][answers][{{ $a->id }}][text]"
                           value="{{ $a->answer_text }}"
                           required
                           class="fi answer-input">
                    <label class="answer-checkbox">
                        <input type="checkbox"
                               name="existing_questions[{{ $q->id }}][answers][{{ $a->id }}][is_correct]"
                               value="1"
                               {{ $a->is_correct ? 'checked' : '' }}
                               class="checkbox-input"> ✓
                    </label>
                </div>
            @endforeach
        </div>
    @endforeach

    {{-- Nouvelles questions --}}
    <div id="qC"></div>

    <button type="button" onclick="addQ()" class="btn-add-question">
        + Nouvelle question
    </button>

    <button type="submit" class="btn-lg">
        💾 Enregistrer
    </button>

</form>

{{-- ✅ JS global — fonctions accessibles par onclick --}}
<script>
var qc = 0;
var ac = {};

function removeEl(id) {
    var el = document.getElementById(id);
    if (el) el.remove();
}

function answerRow(q, a) {
    return '<div class="answer-row" id="ar-' + q + '-' + a + '">'
        + '<input type="text" name="questions[' + q + '][answers][' + a + '][text]" required placeholder="Réponse ' + (a+1) + '" class="fi answer-input">'
        + '<label class="answer-checkbox"><input type="checkbox" name="questions[' + q + '][answers][' + a + '][is_correct]" value="1" class="checkbox-input"> ✓</label>'
        + '<button type="button" onclick="removeEl(\'ar-' + q + '-' + a + '\')" class="btn-delete-inline">✕</button>'
        + '</div>';
}

function generateAnswers(q, n) {
    var html = '';
    for (var j = 0; j < n; j++) html += answerRow(q, j);
    return html;
}

function addAnswer(q) {
    if (!ac[q]) ac[q] = 4;
    var container = document.getElementById('a-' + q);
    if (container) container.insertAdjacentHTML('beforeend', answerRow(q, ac[q]++));
}

function addQ() {
    var i = qc++;
    var container = document.getElementById('qC');
    if (!container) return;

    var html = '<div class="cyber-card question-card" id="q-' + i + '">'
        + '<div class="question-header">Nouvelle question ' + (i+1)
        + ' <button type="button" onclick="removeEl(\'q-' + i + '\')" class="btn-delete-inline">×</button></div>'
        + '<label class="fl no-margin-top">Texte</label>'
        + '<textarea name="questions[' + i + '][question_text]" rows="2" required class="fi"></textarea>'
        + '<div class="form-grid-2">'
        + '<div><label class="fl">Type</label>'
        + '<select name="questions[' + i + '][type]" class="fi">'
        + '<option value="qcm">QCM</option>'
        + '<option value="vrai_faux">Vrai/Faux</option>'
        + '<option value="choix_multiple">Multiple</option>'
        + '</select></div>'
        + '<div><label class="fl">Points</label>'
        + '<input type="number" name="questions[' + i + '][points]" value="1" class="fi"></div>'
        + '</div>'
        + '<label class="fl">Explication</label>'
        + '<input type="text" name="questions[' + i + '][explanation]" class="fi">'
        + '<label class="fl">Réponses</label>'
        + '<div id="a-' + i + '">' + generateAnswers(i, 4) + '</div>'
        + '<button type="button" onclick="addAnswer(' + i + ')" class="btn-add-answer">+ Réponse</button>'
        + '</div>';

    container.insertAdjacentHTML('beforeend', html);
}
</script>

@endsection
