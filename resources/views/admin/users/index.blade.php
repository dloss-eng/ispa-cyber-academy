@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('page-title', '👥 Utilisateurs')

@section('content')

<div class="users-header">

    <form class="users-filter-form">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Rechercher..." class="fi input-search">

        <select name="role" class="fi select-role">
            <option value="">Tous</option>
            @foreach($roles as $r)
                <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>
                    {{ $r->display_name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn-cyber btn-sm">Filtrer</button>
    </form>

    @can('create', \App\Models\User::class)
        <a href="{{ route('admin.users.create') }}" class="btn-cyber btn-sm">+ Ajouter</a>
    @endcan

</div>

<div class="cyber-card table-wrapper">
    <table class="tbl">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Établissement</th>  {{-- ✅ Nouvelle colonne --}}
                <th>Points</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
                <tr>
                    <td class="td-strong">{{ $u->name }}</td>
                    <td class="td-muted">{{ $u->email }}</td>

                    {{-- Rôle --}}
                    <td>
                        <span class="tag {{ $u->role->name === 'admin' ? 'tag-r' : ($u->role->name === 'etablissement' ? 'tag-b' : 'tag-g') }}">
                            {{ $u->role->display_name }}
                        </span>
                    </td>

                    {{-- ✅ Établissement --}}
                    <td>
                        @if($u->etablissement)
                            <span style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:600;color:var(--gr);background:rgba(0,229,160,0.08);border:1px solid rgba(0,229,160,0.2);padding:3px 10px;border-radius:99px;white-space:nowrap">
                                🏫 {{ $u->etablissement->name }}
                            </span>
                        @elseif($u->role->name === 'admin')
                            <span class="td-muted" style="font-size:11px">—</span>
                        @else
                            <span class="td-muted" style="font-size:11px">Non assigné</span>
                        @endif
                    </td>

                    <td>{{ $u->points }}</td>

                    <td class="text-right">

                        @can('update', $u)
                            <a href="{{ route('admin.users.edit', $u) }}" class="link-edit">Modifier</a>
                        @endcan

                        @can('delete', $u)
                            <form action="{{ route('admin.users.destroy', $u) }}"
                                  method="POST" class="inline-form"
                                  onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-delete-text">Supprimer</button>
                            </form>
                        @endcan

                        @cannot('update', $u)
                            <span class="td-muted" title="Lecture seule">🔒</span>
                        @endcannot

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="pagination-wrapper">{{ $users->links() }}</div>

@endsection
