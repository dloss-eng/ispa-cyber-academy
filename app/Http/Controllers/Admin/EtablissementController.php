<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EtablissementController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Etablissement::class);

        $etablissements = Etablissement::withCount('users')->paginate(20);

        return view('admin.etablissements.index', compact('etablissements'));
    }

    public function create()
    {
        $this->authorize('create', Etablissement::class);

        return view('admin.etablissements.form');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Etablissement::class);

        $request->validate([
            'name'                  => 'required|max:255',
            'type'                  => 'required|in:lycee,universite',
            'city'                  => 'required',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8|confirmed',
        ]);

        // ── Logo ──
        $logoPath = null;
        if ($request->hasFile('logo_file')) {
            $logoPath = $request->file('logo_file')->store('logos', 'public');
        }

        // ── Créer l'établissement ──
        $etablissement = Etablissement::create([
            ...$request->only('name', 'type', 'city', 'address', 'phone', 'email'),
            'logo_path' => $logoPath,
        ]);

        // ── Créer le compte utilisateur lié à l'établissement ──
        $role = Role::where('name', 'etablissement')->first();

        if ($role) {
            User::create([
                'name'              => $etablissement->name,
                'email'             => $request->email,
                'password'          => Hash::make($request->password),
                'role_id'           => $role->id,
                'etablissement_id'  => $etablissement->id,
                'is_active'         => true,
            ]);
        }

        return redirect()
            ->route('admin.etablissements.index')
            ->with('success', 'Établissement créé avec son compte de connexion.');
    }

    public function edit(Etablissement $etablissement)
    {
        $this->authorize('update', $etablissement);

        return view('admin.etablissements.form', compact('etablissement'));
    }

    public function update(Request $request, Etablissement $etablissement)
    {
        $this->authorize('update', $etablissement);

        $rules = [
            'name' => 'required|max:255',
            'type' => 'required|in:lycee,universite',
            'city' => 'required',
        ];

        // Valider le mot de passe seulement s'il est renseigné
        if ($request->filled('password')) {
            $rules['password'] = 'min:8|confirmed';
        }

        $request->validate($rules);

        $data = $request->only('name', 'type', 'city', 'address', 'phone', 'email', 'is_active');

        if ($request->hasFile('logo_file')) {
            $data['logo_path'] = $request->file('logo_file')->store('logos', 'public');
        }

        $etablissement->update($data);

        // ── Mettre à jour le mot de passe du compte utilisateur lié ──
        if ($request->filled('password')) {
            $user = User::where('etablissement_id', $etablissement->id)
                        ->whereHas('role', fn($q) => $q->where('name', 'etablissement'))
                        ->first();

            if ($user) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }
        }

        return redirect()
            ->route('admin.etablissements.index')
            ->with('success', 'Établissement modifié.');
    }

    public function destroy(Etablissement $etablissement)
    {
        $this->authorize('delete', $etablissement);

        $etablissement->delete();

        return redirect()
            ->route('admin.etablissements.index')
            ->with('success', 'Supprimé.');
    }
}
