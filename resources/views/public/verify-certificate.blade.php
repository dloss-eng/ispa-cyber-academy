@extends('layouts.landing')

@section('title', 'Vérifier certificat')

@section('content')

<div class="verify-container">

    <div class="section-title">
        🔍 Vérifier un <span class="text-green">Certificat</span>
    </div>

    <div class="cyber-card verify-card">

        <form method="GET" class="verify-form">

            <label class="fl">Numéro du certificat</label>

            <input 
                type="text" 
                name="code" 
                value="{{ request('code') }}" 
                required 
                class="fi"
                placeholder="ISPA-2025-XXXXXXXX"
            >

            <button type="submit" class="btn-lg">
                🔍 Vérifier
            </button>

        </form>

        {{-- RESULT --}}
        @if(request('code'))

            @if(isset($certificate))

                <div class="verify-result success">
                    <div class="icon">✅</div>

                    <div class="title">Certificat valide</div>

                    <div class="user">
                        {{ $certificate->user->name }}
                    </div>

                    <div class="details">
                        {{ $certificate->module->title }} · {{ $certificate->final_score }}%
                    </div>
                </div>

            @else

                <div class="verify-result error">
                    <div class="icon">❌</div>
                    <div class="title">Certificat non trouvé</div>
                </div>

            @endif

        @endif

    </div>

</div>

@endsection


@push('scripts')
<script>
// UX simple
document.addEventListener("DOMContentLoaded", () => {

    const input = document.querySelector('input[name="code"]');

    // auto focus
    input?.focus();

    // format auto (optionnel)
    input?.addEventListener('input', (e) => {
        e.target.value = e.target.value.toUpperCase();
    });

});
</script>
@endpush