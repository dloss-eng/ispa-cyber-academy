@extends('layouts.app')

@section('title', 'Élèves')
@section('page-title', ' Élèves')

@section('content')

{{--  Tableau --}}
<div class="cyber-card table-wrapper">

    <table class="tbl">

        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Points</th>
                <th>Voir</th>
            </tr>
        </thead>

        <tbody>

            @foreach($students as $s)

                <tr>

                    {{--  Nom --}}
                    <td class="td-strong">
                        {{ $s->name }}
                    </td>

                    {{--  Email --}}
                    <td class="td-muted">
                        {{ $s->email }}
                    </td>

                    {{--  Rôle --}}
                    <td>
                        <span class="tag tag-g">
                            {{ $s->role_display }}
                        </span>
                    </td>

                    {{--  Points --}}
                    <td>
                        {{ $s->points }}
                    </td>

                    {{--  Voir --}}
                    <td>
                        <a href="{{ route('enseignant.students.progress',$s) }}"
                           class="table-link">
                            Stats
                        </a>
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>

{{-- 🔢 Pagination --}}
<div class="table-pagination">
    {{ $students->links() }}
</div>

@endsection