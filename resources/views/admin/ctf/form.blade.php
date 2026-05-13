@extends('layouts.app')
@section('title', isset($challenge) ? 'Modifier le challenge' : 'Nouveau challenge CTF')
@section('page-title', isset($challenge) ? '✏️ Modifier — ' . $challenge->title : '🚩 Nouveau challenge CTF')

@section('content')

<div style="max-width:800px;margin:0 auto">

@if($errors->any())
    <div style="background:rgba(255,107,53,0.1);border:1px solid var(--re);border-radius:8px;padding:14px 18px;margin-bottom:20px">
        <div style="font-size:13px;font-weight:700;color:var(--re);margin-bottom:8px">Erreurs de validation :</div>
        <ul style="margin:0;padding-left:16px;font-size:13px;color:var(--re)">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form action="{{ isset($challenge) ? route('admin.ctf.update', $challenge) : route('admin.ctf.store') }}"
      method="POST">
    @csrf
    @if(isset($challenge)) @method('PUT') @endif

    {{-- ── Informations générales ───────────────────────────────── --}}
    <div class="cyber-card" style="margin-bottom:16px">
        <div style="font-size:13px;font-weight:700;color:var(--t2);margin-bottom:16px">Informations générales</div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
            <div>
                <label style="font-size:12px;color:var(--t3);display:block;margin-bottom:6px">Titre *</label>
                <input type="text" name="title" value="{{ old('title', $challenge->title ?? '') }}"
                    placeholder="Ex: Le SMS frauduleux de MTN"
                    style="width:100%;background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:10px 14px;color:var(--t1);font-size:13px;box-sizing:border-box"
                    required>
            </div>
            <div>
                <label style="font-size:12px;color:var(--t3);display:block;margin-bottom:6px">Type *</label>
                <select name="type"
                    style="width:100%;background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:10px 14px;color:var(--t1);font-size:13px"
                    required>
                    <option value="flag_hunt"        {{ old('type', $challenge->type ?? '') === 'flag_hunt'        ? 'selected' : '' }}>🚩 Flag Hunt (trouver le flag caché)</option>
                    <option value="textual_analysis" {{ old('type', $challenge->type ?? '') === 'textual_analysis' ? 'selected' : '' }}>🔍 Analyse textuelle (compter/identifier)</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom:14px">
            <label style="font-size:12px;color:var(--t3);display:block;margin-bottom:6px">Description courte * <span style="color:var(--t3)">(visible dans la liste)</span></label>
            <textarea name="description" rows="2"
                placeholder="Brève description du challenge visible avant de l'ouvrir"
                style="width:100%;background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:10px 14px;color:var(--t1);font-size:13px;resize:vertical;box-sizing:border-box"
                required>{{ old('description', $challenge->description ?? '') }}</textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
            <div>
                <label style="font-size:12px;color:var(--t3);display:block;margin-bottom:6px">Difficulté *</label>
                <select name="difficulty"
                    style="width:100%;background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:10px 14px;color:var(--t1);font-size:13px">
                    <option value="facile"    {{ old('difficulty', $challenge->difficulty ?? 'facile') === 'facile'    ? 'selected' : '' }}>🟢 Facile</option>
                    <option value="moyen"     {{ old('difficulty', $challenge->difficulty ?? '') === 'moyen'     ? 'selected' : '' }}>🟡 Moyen</option>
                    <option value="difficile" {{ old('difficulty', $challenge->difficulty ?? '') === 'difficile' ? 'selected' : '' }}>🔴 Difficile</option>
                </select>
            </div>
            <div>
                <label style="font-size:12px;color:var(--t3);display:block;margin-bottom:6px">Points *</label>
                <input type="number" name="points" min="10" max="1000" step="10"
                    value="{{ old('points', $challenge->points ?? 100) }}"
                    style="width:100%;background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:10px 14px;color:var(--t1);font-size:13px;box-sizing:border-box"
                    required>
            </div>
            <div>
                <label style="font-size:12px;color:var(--t3);display:block;margin-bottom:6px">Max tentatives <span style="color:var(--t3)">(0 = illimité)</span></label>
                <input type="number" name="max_attempts" min="0"
                    value="{{ old('max_attempts', $challenge->max_attempts ?? 0) }}"
                    style="width:100%;background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:10px 14px;color:var(--t1);font-size:13px;box-sizing:border-box">
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:14px">
            <div>
                <label style="font-size:12px;color:var(--t3);display:block;margin-bottom:6px">Module associé <span style="color:var(--t3)">(optionnel)</span></label>
                <select name="module_id"
                    style="width:100%;background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:10px 14px;color:var(--t1);font-size:13px">
                    <option value="">— Standalone (module CTF dédié) —</option>
                    @foreach($modules as $m)
                        <option value="{{ $m->id }}" {{ old('module_id', $challenge->module_id ?? '') == $m->id ? 'selected' : '' }}>
                            {{ $m->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="font-size:12px;color:var(--t3);display:block;margin-bottom:6px">Ordre</label>
                <input type="number" name="order" min="0"
                    value="{{ old('order', $challenge->order ?? 0) }}"
                    style="width:100%;background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:10px 14px;color:var(--t1);font-size:13px;box-sizing:border-box">
            </div>
        </div>

        <div style="margin-top:14px">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
                <input type="checkbox" name="is_published" value="1"
                    {{ old('is_published', $challenge->is_published ?? false) ? 'checked' : '' }}
                    style="accent-color:var(--gr);width:16px;height:16px">
                Publier ce challenge (visible aux étudiants)
            </label>
        </div>
    </div>

    {{-- ── Scénario ─────────────────────────────────────────────── --}}
    <div class="cyber-card" style="margin-bottom:16px">
        <div style="font-size:13px;font-weight:700;color:var(--t2);margin-bottom:4px">Scénario du challenge *</div>
        <div style="font-size:11px;color:var(--t3);margin-bottom:12px">HTML supporté. Décrivez le scénario, les instructions et ce que l'étudiant doit trouver.</div>
        <textarea name="scenario" rows="12"
            placeholder="<h2>Votre mission</h2><p>Analysez ce SMS...</p>"
            style="width:100%;background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:12px 14px;color:var(--t1);font-size:13px;font-family:monospace;resize:vertical;box-sizing:border-box"
            required>{{ old('scenario', $challenge->scenario ?? '') }}</textarea>
    </div>

    {{-- ── Flag ─────────────────────────────────────────────────── --}}
    <div class="cyber-card" style="margin-bottom:16px">
        <div style="font-size:13px;font-weight:700;color:var(--t2);margin-bottom:4px">Flag attendu *</div>
        <div style="font-size:11px;color:var(--t3);margin-bottom:12px">
            La réponse correcte. Format recommandé : <code style="color:var(--bl)">ISPA{MOT_CLE}</code>
            — La vérification est insensible à la casse.
        </div>
        <input type="text" name="flag"
            value="{{ old('flag', $challenge->flag ?? '') }}"
            placeholder="ISPA{FLAG_ICI}"
            style="width:100%;background:rgba(255,255,255,0.04);border:1px solid rgba(255,107,53,0.4);border-radius:8px;padding:12px 14px;color:var(--gr);font-family:monospace;font-size:14px;box-sizing:border-box"
            required>
        <div style="font-size:11px;color:var(--t3);margin-top:6px">⚠️ Ce champ est confidentiel — ne sera jamais affiché aux étudiants</div>
    </div>

    {{-- ── Indices ──────────────────────────────────────────────── --}}
    <div class="cyber-card" style="margin-bottom:20px">
        <div style="font-size:13px;font-weight:700;color:var(--t2);margin-bottom:4px">Indices <span style="color:var(--t3);font-weight:400">(optionnels)</span></div>
        <div style="font-size:11px;color:var(--t3);margin-bottom:14px">Chaque indice révélé réduit les points gagnés du coût configuré.</div>

        <div id="hints-list">
            @php $existingHints = old('hint_texts') ? array_map(null, old('hint_texts', []), old('hint_costs', [])) : ($challenge->hints ?? []) @endphp
            @foreach($existingHints as $i => $hint)
            <div class="hint-row" style="display:grid;grid-template-columns:1fr auto;gap:10px;margin-bottom:10px;align-items:center">
                <input type="text" name="hint_texts[]"
                    value="{{ is_array($hint) ? ($hint['text'] ?? '') : '' }}"
                    placeholder="Texte de l'indice {{ $i + 1 }}"
                    style="background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:10px 14px;color:var(--t1);font-size:13px;width:100%;box-sizing:border-box">
                <div style="display:flex;gap:6px;align-items:center">
                    <input type="number" name="hint_costs[]" min="0" max="100"
                        value="{{ is_array($hint) ? ($hint['cost_points'] ?? 10) : 10 }}"
                        placeholder="pts"
                        style="width:70px;background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:10px 10px;color:var(--ye);font-size:13px;text-align:center">
                    <button type="button" onclick="this.closest('.hint-row').remove()"
                        style="background:rgba(255,107,53,0.1);border:1px solid rgba(255,107,53,0.3);border-radius:6px;padding:8px 12px;color:var(--re);cursor:pointer;font-size:13px">✕</button>
                </div>
            </div>
            @endforeach
        </div>

        <button type="button" onclick="addHint()"
            style="background:rgba(75,123,255,0.08);border:1px dashed rgba(75,123,255,0.3);border-radius:8px;padding:10px 16px;color:var(--bl);cursor:pointer;font-size:13px;width:100%;margin-top:4px">
            + Ajouter un indice
        </button>
    </div>

    {{-- ── Submit ───────────────────────────────────────────────── --}}
    <div style="display:flex;gap:12px">
        <button type="submit" class="btn-cyber" style="flex:1;justify-content:center">
            {{ isset($challenge) ? '💾 Enregistrer les modifications' : '🚀 Créer le challenge' }}
        </button>
        <a href="{{ route('admin.ctf.index') }}"
           style="background:rgba(255,255,255,0.05);border:1px solid var(--bd);border-radius:8px;padding:10px 20px;color:var(--t2);font-size:14px;display:flex;align-items:center;text-decoration:none">
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
    row.style = 'display:grid;grid-template-columns:1fr auto;gap:10px;margin-bottom:10px;align-items:center';
    row.innerHTML = `
        <input type="text" name="hint_texts[]" placeholder="Texte de l'indice ${count}"
            style="background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:10px 14px;color:var(--t1);font-size:13px;width:100%;box-sizing:border-box">
        <div style="display:flex;gap:6px;align-items:center">
            <input type="number" name="hint_costs[]" min="0" max="100" value="10" placeholder="pts"
                style="width:70px;background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:10px 10px;color:var(--ye);font-size:13px;text-align:center">
            <button type="button" onclick="this.closest('.hint-row').remove()"
                style="background:rgba(255,107,53,0.1);border:1px solid rgba(255,107,53,0.3);border-radius:6px;padding:8px 12px;color:var(--re);cursor:pointer;font-size:13px">✕</button>
        </div>`;
    list.appendChild(row);
}
</script>
@endpush

@endsection
