@extends('layouts.app')
@section('title', isset($challenge) ? 'Modifier le challenge' : 'Nouveau challenge CTF')
@section('page-title', isset($challenge) ? ' Modifier — ' . $challenge->title : '🚩 Nouveau challenge CTF')

@section('content')

<div class="ctf-form-wrap">

@if($errors->any())
    <div class="ctf-errors">
        <div class="ctf-errors-title">Erreurs de validation :</div>
        <ul class="ctf-errors-list">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form action="{{ isset($challenge) ? route('admin.ctf.update', $challenge) : route('admin.ctf.store') }}"
      method="POST">
    @csrf
    @if(isset($challenge)) @method('PUT') @endif

    {{-- ── Informations générales ──── --}}
    <div class="cyber-card ctf-section">
        <div class="ctf-section-title">Informations générales</div>

        <div class="ctf-grid-2 ctf-mb">
            <div>
                <label class="ctf-label">Titre *</label>
                <input type="text" name="title" value="{{ old('title', $challenge->title ?? '') }}"
                    placeholder="Ex: Le SMS frauduleux de MTN"
                    class="ctf-input" required>
            </div>
            <div>
                <label class="ctf-label">Type *</label>
                <select name="type" class="ctf-input" required>
                    <option value="flag_hunt"        {{ old('type', $challenge->type ?? '') === 'flag_hunt'        ? 'selected' : '' }}>🚩 Flag Hunt (trouver le flag caché)</option>
                    <option value="textual_analysis" {{ old('type', $challenge->type ?? '') === 'textual_analysis' ? 'selected' : '' }}>🔍 Analyse textuelle (compter/identifier)</option>
                </select>
            </div>
        </div>

        <div class="ctf-mb">
            <label class="ctf-label">Description courte * <span class="ctf-label-hint">(visible dans la liste)</span></label>
            <textarea name="description" rows="2" placeholder="Brève description du challenge visible avant de l'ouvrir"
                class="ctf-input ctf-textarea" required>{{ old('description', $challenge->description ?? '') }}</textarea>
        </div>

        <div class="ctf-grid-3">
            <div>
                <label class="ctf-label">Difficulté *</label>
                <select name="difficulty" class="ctf-input">
                    <option value="facile"    {{ old('difficulty', $challenge->difficulty ?? 'facile') === 'facile'    ? 'selected' : '' }}>🟢 Facile</option>
                    <option value="moyen"     {{ old('difficulty', $challenge->difficulty ?? '') === 'moyen'     ? 'selected' : '' }}>🟡 Moyen</option>
                    <option value="difficile" {{ old('difficulty', $challenge->difficulty ?? '') === 'difficile' ? 'selected' : '' }}>🔴 Difficile</option>
                </select>
            </div>
            <div>
                <label class="ctf-label">Points *</label>
                <input type="number" name="points" min="10" max="1000" step="10"
                    value="{{ old('points', $challenge->points ?? 100) }}"
                    class="ctf-input" required>
            </div>
            <div>
                <label class="ctf-label">Max tentatives <span class="ctf-label-hint">(0 = illimité)</span></label>
                <input type="number" name="max_attempts" min="0"
                    value="{{ old('max_attempts', $challenge->max_attempts ?? 0) }}"
                    class="ctf-input">
            </div>
        </div>

        <div class="ctf-grid-2 ctf-mt">
            <div>
                <label class="ctf-label">Module associé <span class="ctf-label-hint">(optionnel)</span></label>
                <select name="module_id" class="ctf-input">
                    <option value="">— Standalone (module CTF dédié) —</option>
                    @foreach($modules as $m)
                        <option value="{{ $m->id }}" {{ old('module_id', $challenge->module_id ?? '') == $m->id ? 'selected' : '' }}>
                            {{ $m->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="ctf-label">Ordre</label>
                <input type="number" name="order" min="0"
                    value="{{ old('order', $challenge->order ?? 0) }}"
                    class="ctf-input">
            </div>
        </div>

        <div class="ctf-mt">
            <label class="ctf-checkbox-label">
                <input type="checkbox" name="is_published" value="1"
                    {{ old('is_published', $challenge->is_published ?? false) ? 'checked' : '' }}
                    class="ctf-checkbox">
                Publier ce challenge (visible aux étudiants)
            </label>
        </div>
    </div>

    {{-- ── Scénario ──── --}}
    <div class="cyber-card ctf-section">
        <div class="ctf-section-title">Scénario du challenge *</div>
        <div class="ctf-hint-text ctf-mb-sm">HTML supporté. Décrivez le scénario, les instructions et ce que l'étudiant doit trouver.</div>
        <textarea name="scenario" rows="12"
            placeholder="<h2>Votre mission</h2><p>Analysez ce SMS...</p>"
            class="ctf-input ctf-textarea ctf-mono" required>{{ old('scenario', $challenge->scenario ?? '') }}</textarea>
    </div>

    {{-- ── Flag ───── --}}
    <div class="cyber-card ctf-section">
        <div class="ctf-section-title">Flag attendu *</div>
        <div class="ctf-hint-text ctf-mb-sm">
            La réponse correcte. Format recommandé : <code class="ctf-code">ISPA{MOT_CLE}</code>
            — La vérification est insensible à la casse.
        </div>
        <input type="text" name="flag"
            value="{{ old('flag', $challenge->flag ?? '') }}"
            placeholder="ISPA{FLAG_ICI}"
            class="ctf-input ctf-flag-input" required>
        <div class="ctf-warning-text">⚠️ Ce champ est confidentiel — ne sera jamais affiché aux étudiants</div>
    </div>

    {{-- ── Indices ─── --}}
    <div class="cyber-card ctf-section ctf-section-last">
        <div class="ctf-section-title">Indices <span class="ctf-optional">(optionnels)</span></div>
        <div class="ctf-hint-text ctf-mb-sm">Chaque indice révélé réduit les points gagnés du coût configuré.</div>

        <div id="hints-list">
            @php
                $existingHints = [];
                $oldTexts = old('hint_texts');
                if ($oldTexts) {
                    $oldCosts = old('hint_costs', []);
                    foreach ($oldTexts as $i => $text) {
                        $existingHints[] = [
                            'text'        => $text,
                            'cost_points' => $oldCosts[$i] ?? 10,
                        ];
                    }
                } else {
                    $rawHints = isset($challenge) ? ($challenge->hints ?? []) : [];
                    $existingHints = is_array($rawHints) ? $rawHints : [];
                }
            @endphp

            @foreach($existingHints as $i => $hint)
            <div class="hint-row">
                <input type="text" name="hint_texts[]"
                    value="{{ is_array($hint) ? ($hint['text'] ?? '') : '' }}"
                    placeholder="Texte de l'indice {{ $i + 1 }}"
                    class="ctf-input">
                <div class="hint-row-actions">
                    <input type="number" name="hint_costs[]" min="0" max="100"
                        value="{{ is_array($hint) ? ($hint['cost_points'] ?? 10) : 10 }}"
                        placeholder="pts" class="ctf-input ctf-cost-input">
                    <button type="button" onclick="this.closest('.hint-row').remove()" class="ctf-btn-remove">✕</button>
                </div>
            </div>
            @endforeach
        </div>

        <button type="button" onclick="addHint()" class="ctf-btn-add-hint">
            + Ajouter un indice
        </button>
    </div>

    {{-- ── Submit ───── --}}
    <div class="ctf-submit-row">
        <button type="submit" class="btn-cyber ctf-btn-submit">
            {{ isset($challenge) ? ' Enregistrer les modifications' : ' Créer le challenge' }}
        </button>
        <a href="{{ route('admin.ctf.index') }}" class="ctf-btn-cancel">
            Annuler
        </a>
    </div>

</form>
</div>

@push('scripts')
<script>
function addHint() {
    const list  = document.getElementById('hints-list');
    const count = list.querySelectorAll('.hint-row').length + 1;
    const row   = document.createElement('div');
    row.className = 'hint-row';
    row.innerHTML = `
        <input type="text" name="hint_texts[]" placeholder="Texte de l'indice ${count}" class="ctf-input">
        <div class="hint-row-actions">
            <input type="number" name="hint_costs[]" min="0" max="100" value="10" placeholder="pts" class="ctf-input ctf-cost-input">
            <button type="button" onclick="this.closest('.hint-row').remove()" class="ctf-btn-remove">✕</button>
        </div>`;
    list.appendChild(row);
}
</script>
@endpush

@push('styles')
<style>
/* ── Wrapper ─── */
.ctf-form-wrap { max-width: 800px; margin: 0 auto; }

/* ── Erreurs ─── */
.ctf-errors {
    background: rgba(255,107,53,0.1);
    border: 1px solid var(--re);
    border-radius: 8px;
    padding: 14px 18px;
    margin-bottom: 20px;
}
.ctf-errors-title { font-size: 13px; font-weight: 700; color: var(--re); margin-bottom: 8px; }
.ctf-errors-list  { margin: 0; padding-left: 16px; font-size: 13px; color: var(--re); }

/* ── Sections ──── */
.ctf-section      { margin-bottom: 16px; }
.ctf-section-last { margin-bottom: 20px; }
.ctf-section-title { font-size: 13px; font-weight: 700; color: var(--t2); margin-bottom: 16px; }

/* ── Labels ── */
.ctf-label       { font-size: 12px; color: var(--t3); display: block; margin-bottom: 6px; }
.ctf-label-hint  { color: var(--t3); }
.ctf-hint-text   { font-size: 11px; color: var(--t3); }
.ctf-warning-text { font-size: 11px; color: var(--t3); margin-top: 6px; }
.ctf-optional    { color: var(--t3); font-weight: 400; }

/* ── Inputs communs ── */
.ctf-input {
    width: 100%;
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--bd);
    border-radius: 8px;
    padding: 10px 14px;
    color: var(--t1);
    font-size: 13px;
    box-sizing: border-box;
}
.ctf-textarea   { resize: vertical; }
.ctf-mono       { font-family: monospace; }
.ctf-flag-input {
    border-color: rgba(255,107,53,0.4);
    color: var(--gr);
    font-family: monospace;
    font-size: 14px;
    padding: 12px 14px;
}
.ctf-code { color: var(--bl); }

/* ── Grilles ──── */
.ctf-grid-2 { display: grid; grid-template-columns: 1fr 1fr;     gap: 14px; }
.ctf-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }

/* ── Espacements ──── */
.ctf-mb    { margin-bottom: 14px; }
.ctf-mb-sm { margin-bottom: 12px; }
.ctf-mt    { margin-top: 14px; }

/* ── Checkbox publication ───── */
.ctf-checkbox-label { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; }
.ctf-checkbox       { accent-color: var(--gr); width: 16px; height: 16px; }

/* ── Indices ───── */
.hint-row {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 10px;
    margin-bottom: 10px;
    align-items: center;
}
.hint-row-actions { display: flex; gap: 6px; align-items: center; }
.ctf-cost-input   { width: 70px !important; color: var(--ye); text-align: center; }
.ctf-btn-remove {
    background: rgba(255,107,53,0.1);
    border: 1px solid rgba(255,107,53,0.3);
    border-radius: 6px;
    padding: 8px 12px;
    color: var(--re);
    cursor: pointer;
    font-size: 13px;
}
.ctf-btn-add-hint {
    background: rgba(75,123,255,0.08);
    border: 1px dashed rgba(75,123,255,0.3);
    border-radius: 8px;
    padding: 10px 16px;
    color: var(--bl);
    cursor: pointer;
    font-size: 13px;
    width: 100%;
    margin-top: 4px;
}

/* ── Boutons submit ───── */
.ctf-submit-row  { display: flex; gap: 12px; }
.ctf-btn-submit  { flex: 1; justify-content: center; }
.ctf-btn-cancel  {
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--bd);
    border-radius: 8px;
    padding: 10px 20px;
    color: var(--t2);
    font-size: 14px;
    display: flex;
    align-items: center;
    text-decoration: none;
}
</style>
@endpush

@endsection
