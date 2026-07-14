@extends('layouts.app')

@section('title', 'Créer Quiz')
@section('page-title', '📝 Créer un Quiz')

@section('content')

{{--  Contexte : leçon OU module --}}
<div class="quiz-lesson-info">
    @isset($lesson)
        Leçon : <strong class="quiz-lesson-title">{{ $lesson->title }}</strong>
    @else
        Module : <strong class="quiz-lesson-title">🏆 Quiz Final — {{ $module->title }}</strong>
    @endisset
</div>

{{--  Action selon le contexte --}}
<form method="POST"
      action="{{ isset($lesson)
          ? route('admin.lessons.quiz.store', $lesson)
          : route('admin.modules.quiz.store', $module) }}"
      id="quizForm">
    @csrf

    {{-- Paramètres --}}
    <div class="cyber-card quiz-card">

        <div class="form-grid-2">
            <div>
                <label class="fl no-margin-top">Titre</label>
                <input type="text" name="title" required class="fi">
            </div>
            <div>
                <label class="fl no-margin-top">Score requis (%)</label>
                <input type="number" name="passing_score" value="70" min="0" max="100" class="fi">
            </div>
        </div>

        <div class="form-grid-2">
            <div>
                <label class="fl">Temps (min)</label>
                <input type="number" name="time_limit_minutes" value="15" class="fi">
            </div>
            <div>
                <label class="fl">Max tentatives</label>
                <input type="number" name="max_attempts" value="3" class="fi">
            </div>
        </div>

        <label class="checkbox-inline">
            <input type="checkbox" name="is_published" value="1" checked class="checkbox-input">
            Publier
        </label>

    </div>

    {{-- Questions --}}
    <div id="qC"></div>

    <button type="button" id="addQuestionBtn" class="btn-add-question">
        + Nouvelle question
    </button>

    <button type="submit" class="btn-lg">
         Enregistrer
    </button>

</form>

@endsection

@push('scripts')
<script>
let qc = 0;
const ac = {};

function answerRow(q, a) {
    return `<div class="answer-row" id="ar-${q}-${a}">
        <input type="text" name="questions[${q}][answers][${a}][text]" required placeholder="Réponse ${a+1}" class="fi answer-input">
        <label class="answer-checkbox"><input type="checkbox" name="questions[${q}][answers][${a}][is_correct]" value="1" class="checkbox-input"> ✓</label>
        <button type="button" onclick="document.getElementById('ar-${q}-${a}').remove()" class="btn-delete-inline">✕</button>
    </div>`;
}

function generateAnswers(q, n) {
    let html = '';
    for (let j = 0; j < n; j++) html += answerRow(q, j);
    return html;
}

function addAnswer(q) {
    if (!ac[q]) ac[q] = 4;
    document.getElementById('a-' + q).insertAdjacentHTML('beforeend', answerRow(q, ac[q]++));
}

function addQ() {
    const i = qc++;
    document.getElementById('qC').insertAdjacentHTML('beforeend', `
        <div class="cyber-card question-card" id="q-${i}">
            <div class="question-header">Question ${i+1}
                <button type="button" onclick="document.getElementById('q-${i}').remove()" class="btn-delete-inline">×</button>
            </div>
            <label class="fl no-margin-top">Texte</label>
            <textarea name="questions[${i}][question_text]" rows="2" required class="fi"></textarea>
            <div class="form-grid-2">
                <div><label class="fl">Type</label>
                <select name="questions[${i}][type]" class="fi">
                    <option value="qcm">QCM</option>
                    <option value="vrai_faux">Vrai/Faux</option>
                    <option value="choix_multiple">Multiple</option>
                </select></div>
                <div><label class="fl">Points</label>
                <input type="number" name="questions[${i}][points]" value="1" class="fi"></div>
            </div>
            <label class="fl">Explication</label>
            <input type="text" name="questions[${i}][explanation]" class="fi">
            <label class="fl">Réponses</label>
            <div id="a-${i}">${generateAnswers(i, 4)}</div>
            <button type="button" onclick="addAnswer(${i})" class="btn-add-answer">+ Réponse</button>
        </div>`);
}

document.getElementById('addQuestionBtn').addEventListener('click', addQ);
addQ();
</script>
@endpush
