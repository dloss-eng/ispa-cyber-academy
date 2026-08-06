@extends('layouts.app')
@section('title', $challenge->title)
@section('page-title', ' CTF — ' . $challenge->title)

@section('content')

<div style="max-width:800px;margin:0 auto">

    {{-- ── Alertes ──────────────────────────────────────────────── --}}
    @if(session('success'))
        <div style="background:rgba(0,229,160,0.1);border:1px solid var(--gr);border-radius:10px;padding:14px 18px;margin-bottom:20px;color:var(--gr);font-weight:600">
            {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div style="background:rgba(75,123,255,0.1);border:1px solid var(--bl);border-radius:10px;padding:14px 18px;margin-bottom:20px;color:var(--bl)">
            {{ session('info') }}
        </div>
    @endif
    @if($errors->has('flag'))
        <div style="background:rgba(255,107,53,0.1);border:1px solid var(--re);border-radius:10px;padding:14px 18px;margin-bottom:20px;color:var(--re);font-weight:600">
            {{ $errors->first('flag') }}
        </div>
    @endif

    {{-- ── En-tête challenge ───────────────────────────────────── --}}
    <div class="cyber-card" style="padding:0;overflow:hidden;margin-bottom:20px">

        <div style="background:{{ match($challenge->difficulty) {
            'facile'    => 'linear-gradient(135deg,rgba(0,229,160,0.12),rgba(0,229,160,0.03))',
            'moyen'     => 'linear-gradient(135deg,rgba(255,215,0,0.12),rgba(255,215,0,0.03))',
            'difficile' => 'linear-gradient(135deg,rgba(255,107,53,0.15),rgba(255,107,53,0.04))',
        } }};padding:20px 24px;border-bottom:1px solid var(--bd)">

            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:10px">
                <div>
                    <div style="font-size:18px;font-weight:800;margin-bottom:6px">
                        {{ $challenge->typeIcon() }} {{ $challenge->title }}
                    </div>
                    <div style="font-size:12px;color:var(--t2)">{{ $challenge->description }}</div>
                </div>
                <div style="text-align:right;flex-shrink:0">
                    <div style="font-size:22px;font-weight:900;color:var(--ye)">{{ $challenge->points }} pts</div>
                    <span class="tag {{ match($challenge->difficulty) {
                        'facile'    => 'tag-g',
                        'moyen'     => 'tag-y',
                        'difficile' => 'tag-r',
                        default     => 'tag-y'
                    } }}">{{ ucfirst($challenge->difficulty) }}</span>
                </div>
            </div>

            <div style="display:flex;gap:20px;margin-top:14px;font-size:11px;color:var(--t3)">
                <span>{{ $challenge->type === 'flag_hunt' ? '🚩 Flag Hunt' : '🔍 Analyse textuelle' }}</span>
                @if($challenge->max_attempts > 0)
                    <span>🔄 {{ $remaining !== null ? $remaining : '∞' }} tentative(s) restante(s)</span>
                @else
                    <span>🔄 Tentatives illimitées</span>
                @endif
                @if($hints)
                    <span>💡 {{ count($hints) }} indice(s) disponible(s)</span>
                @endif
            </div>
        </div>

        {{-- ── Scénario ─────────────────────────────────────────── --}}
        <div style="padding:24px;font-size:14px;line-height:1.7;color:var(--t1)">
            {!! $challenge->scenario !!}
        </div>

    </div>

    {{-- ── Indices ──────────────────────────────────────────────── --}}
    @if($hints && count($hints) > 0 && !$isSolved)
    <div class="cyber-card" style="margin-bottom:20px">
        <div style="font-size:13px;font-weight:700;margin-bottom:14px;color:var(--ye)">💡 Indices disponibles</div>
        <div style="display:flex;flex-direction:column;gap:10px">
            @foreach($hints as $i => $hint)
            <div id="hint-wrapper-{{ $i }}">
                @if($i < $hintsUsed)
                    {{-- Déjà révélé --}}
                    <div style="background:rgba(255,215,0,0.07);border:1px solid rgba(255,215,0,0.2);border-radius:8px;padding:12px 14px;font-size:13px;color:var(--ye)">
                        💡 <strong>Indice {{ $i + 1 }} :</strong> {{ $hint['text'] }}
                    </div>
                @else
                    {{-- Bouton révélation --}}
                    <button
                        onclick="revealHint({{ $i }}, {{ $hint['cost_points'] ?? 10 }})"
                        id="hint-btn-{{ $i }}"
                        style="width:100%;background:rgba(255,215,0,0.05);border:1px dashed rgba(255,215,0,0.3);border-radius:8px;padding:12px 14px;font-size:13px;color:var(--t2);cursor:pointer;text-align:left">
                        🔒 Indice {{ $i + 1 }} — Révéler (-{{ $hint['cost_points'] ?? 10 }} pts)
                    </button>
                    <div id="hint-content-{{ $i }}" style="display:none;background:rgba(255,215,0,0.07);border:1px solid rgba(255,215,0,0.2);border-radius:8px;padding:12px 14px;font-size:13px;color:var(--ye)"></div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Soumission du flag ───────────────────────────────────── --}}
    @if($isSolved)
        <div class="cyber-card" style="text-align:center;padding:32px;border-color:rgba(0,229,160,0.4)">
            <div style="font-size:48px;margin-bottom:12px">🎉</div>
            <div style="font-size:18px;font-weight:800;color:var(--gr);margin-bottom:8px">Challenge résolu !</div>
            <div style="font-size:13px;color:var(--t3)">Vous avez trouvé le bon flag. Bien joué !</div>
            <a href="{{ route('ctf.index') }}" class="btn-cyber" style="margin-top:20px;display:inline-flex">← Retour aux challenges</a>
        </div>

    @elseif($remaining === 0)
        <div class="cyber-card" style="text-align:center;padding:32px;border-color:rgba(255,107,53,0.4)">
            <div style="font-size:48px;margin-bottom:12px">💀</div>
            <div style="font-size:18px;font-weight:800;color:var(--re);margin-bottom:8px">Tentatives épuisées</div>
            <div style="font-size:13px;color:var(--t3)">Vous avez atteint le nombre maximum de tentatives.</div>
            <a href="{{ route('ctf.index') }}" class="btn-cyber" style="margin-top:20px;display:inline-flex">← Autres challenges</a>
        </div>

    @else
        <div class="cyber-card">
            <div style="font-size:13px;font-weight:700;margin-bottom:16px">🚩 Soumettre votre flag</div>

            <form action="{{ route('ctf.submit', $challenge) }}" method="POST">
                @csrf
                <input type="hidden" name="hints_used" id="hintsUsedInput" value="{{ $hintsUsed }}">

                <div style="display:flex;gap:10px">
                    <input
                        type="text"
                        name="flag"
                        placeholder="ISPA{votre_flag_ici}"
                        value="{{ old('flag') }}"
                        style="flex:1;background:rgba(255,255,255,0.04);border:1px solid var(--bd);border-radius:8px;padding:12px 16px;color:var(--t1);font-family:monospace;font-size:14px"
                        autocomplete="off"
                        spellcheck="false"
                        required
                    >
                    <button type="submit" class="btn-cyber" onclick="return confirm('Soumettre ce flag ?')">
                        📤 Valider
                    </button>
                </div>

                <div style="font-size:11px;color:var(--t3);margin-top:8px">
                    Format attendu : <code style="color:var(--bl)">ISPA{...}</code> — La vérification est insensible à la casse
                </div>
            </form>
        </div>
    @endif

    {{-- ── Historique des tentatives ────────────────────────────── --}}
    @if($history->count() > 0)
    <div class="cyber-card" style="margin-top:20px">
        <div style="font-size:13px;font-weight:700;margin-bottom:14px;color:var(--t2)">📋 Historique des tentatives</div>
        <div style="display:flex;flex-direction:column;gap:6px">
            @foreach($history as $attempt)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 12px;background:rgba(255,255,255,0.02);border-radius:6px;font-size:12px">
                <span style="font-family:monospace;color:{{ $attempt->is_correct ? 'var(--gr)' : 'var(--re)' }}">
                    {{ $attempt->is_correct ? '✅' : '❌' }} {{ Str::limit($attempt->submitted_flag, 30) }}
                </span>
                <span style="color:var(--t3)">{{ $attempt->created_at->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Navigation ──────────────────────────────────────────── --}}
    <div style="margin-top:20px">
        <a href="{{ route('ctf.index') }}" style="font-size:13px;color:var(--t3)">← Retour aux challenges</a>
    </div>

</div>

@push('scripts')
<script>
let hintsUsed = {{ $hintsUsed }};

function revealHint(index, cost) {
    if (!confirm(`Révéler cet indice ? (-${cost} points)`)) return;

    fetch('{{ route('ctf.hint', $challenge) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ index })
    })
    .then(r => r.json())
    .then(data => {
        if (data.hint) {
            hintsUsed++;
            document.getElementById('hintsUsedInput').value = hintsUsed;

            const btn     = document.getElementById(`hint-btn-${index}`);
            const content = document.getElementById(`hint-content-${index}`);

            btn.style.display = 'none';
            content.innerHTML = `💡 <strong>Indice ${index + 1} :</strong> ${data.hint}`;
            content.style.display = 'block';
        }
    })
    .catch(() => alert('Erreur lors de la récupération de l\'indice.'));
}
</script>
@endpush

@endsection
