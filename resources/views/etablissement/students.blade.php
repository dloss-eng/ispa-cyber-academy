@extends('layouts.app')

@section('title', 'Élèves')
@section('page-title', '🎓 Mes Élèves')

@section('content')

{{-- ➕ bouton --}}
<div class="header-action">
    <a href="{{ route('etablissement.students.create') }}" class="btn-cyber btn-sm">
        + Ajouter
    </a>
</div>

{{-- 📋 tableau --}}
<div class="cyber-card table-container">
    <table class="tbl">

        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Points</th>
                <th>Progression</th>
            </tr>
        </thead>

        <tbody>
        @foreach($students as $s)
            <tr>

                {{-- Nom --}}
                <td class="td-name">
                    <a href="{{ route('etablissement.students.progress',$s) }}" class="link-name">
                        {{ $s->name }}
                    </a>
                </td>

                {{-- Email --}}
                <td class="td-email">
                    {{ $s->email }}
                </td>

                {{-- Rôle --}}
                <td>
                    <span class="tag tag-g">{{ $s->role_display }}</span>
                </td>

                {{-- Points --}}
                <td>
                    {{ $s->points }}
                </td>

                {{-- Actions --}}
                <td class="td-actions">

                    <a href="{{ route('etablissement.students.progress',$s) }}" class="action-view">
                        📊
                    </a>

                    <a href="{{ route('etablissement.students.edit',$s) }}" class="action-edit">
                        ✏️
                    </a>

                    <form action="{{ route('etablissement.students.destroy',$s) }}" method="POST" class="inline-form" onsubmit="return confirm('Supprimer ?')">
                        @csrf
                        @method('DELETE')
                        <button class="action-delete">Supprimer</button>
                    </form>

                </td>

            </tr>
        @endforeach
        </tbody>

    </table>
</div>

{{-- pagination --}}
<div class="pagination-container">
    {{ $students->links() }}
</div>

@endsection