@extends('layouts.app')

@section('title', 'Stats '.$classe->name)
@section('page-title', ' Stats — '.$classe->name)

@section('content')

{{--  Retour --}}
<a href="{{ route('etablissement.classes') }}" class="back-link">
    ← Classes
</a>

{{--  Tableau --}}
<div class="cyber-card table-wrapper">

    <table class="tbl">

        <thead>
            <tr>
                <th>Élève</th>
                <th>Points</th>
                <th>Leçons</th>
                <th>Quiz réussis</th>
                <th>Badges</th>
            </tr>
        </thead>

        <tbody>

            @foreach($stats as $s)

                <tr>

                    {{--  Élève --}}
                    <td class="td-strong">
                        {{ $s['user']->name }}
                    </td>

                    {{--  Points --}}
                    <td class="td-success">
                        {{ $s['points'] }}
                    </td>

                    {{--  Leçons --}}
                    <td>
                        {{ $s['progress'] }}
                    </td>

                    {{--  Quiz --}}
                    <td>
                        {{ $s['quizzes'] }}
                    </td>

                    {{--  Badges --}}
                    <td>
                        {{ $s['user']->badges->count() }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>

@endsection