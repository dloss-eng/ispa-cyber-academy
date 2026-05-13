<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Role, Etablissement};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, DB};

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::with(['role', 'etablissement']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name',  'like', '%'.$request->search.'%')
                  ->orWhere('email','like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', fn($q) => $q->where('name', $request->role));
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::select('id', 'name', 'display_name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return view('admin.users.create', [
            'roles'          => Role::select('id', 'name', 'display_name')->get(),
            'etablissements' => Etablissement::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'password'         => ['required', \Illuminate\Validation\Rules\Password::min(12)->mixedCase()->numbers()->symbols(), 'confirmed'],
            'role_id'          => 'required|exists:roles,id',
            'etablissement_id' => 'nullable|exists:etablissements,id',
            'phone'            => 'nullable|string|max:20',
            // Champs établissement (si rôle = etablissement)
            'etab_type'        => 'nullable|in:lycee,universite',
            'etab_city'        => 'nullable|string|max:100',
        ]);

        $data['password'] = Hash::make($data['password']);

        DB::transaction(function () use ($data, $request) {

            // ✅ Si le rôle est "etablissement", créer automatiquement l'établissement
            $role = Role::find($data['role_id']);

            if ($role && $role->name === 'etablissement' && empty($data['etablissement_id'])) {
                $etab = Etablissement::create([
                    'name'      => $data['name'],
                    'type'      => $request->etab_type ?? 'universite',
                    'city'      => $request->etab_city ?? '',
                    'is_active' => true,
                ]);
                $data['etablissement_id'] = $etab->id;
            }

            // Nettoyer les champs non-utilisateurs
            unset($data['etab_type'], $data['etab_city']);

            User::create($data);
        });

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur créé.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', [
            'user'           => $user,
            'roles'          => Role::select('id', 'name', 'display_name')->get(),
            'etablissements' => Etablissement::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,'.$user->id,
            'password'         => ['nullable', \Illuminate\Validation\Rules\Password::min(12)->mixedCase()->numbers()->symbols(), 'confirmed'],
            'role_id'          => 'required|exists:roles,id',
            'etablissement_id' => 'nullable|exists:etablissements,id',
            'phone'            => 'nullable|string|max:20',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        DB::transaction(function () use ($data, $user) {

            // ✅ Si passage au rôle "etablissement" sans établissement lié → créer automatiquement
            $role = Role::find($data['role_id']);

            if ($role && $role->name === 'etablissement' && empty($data['etablissement_id'])) {
                // Vérifier si un établissement existe déjà pour cet utilisateur
                if (!$user->etablissement_id) {
                    $etab = Etablissement::create([
                        'name'      => $user->name,
                        'type'      => 'universite',
                        'is_active' => true,
                    ]);
                    $data['etablissement_id'] = $etab->id;
                } else {
                    // Garder l'établissement existant
                    $data['etablissement_id'] = $user->etablissement_id;
                }
            }

            $user->update($data);
        });

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur modifié.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->isAdmin()) {
            return back()->withErrors(['user' => 'Impossible de supprimer un administrateur.']);
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'Vous ne pouvez pas vous supprimer vous-même.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
    }
}
