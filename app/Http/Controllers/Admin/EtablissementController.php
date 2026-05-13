<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use Illuminate\Http\Request;

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
            'name'=>'required|max:255',
            'type'=>'required|in:lycee,universite',
            'city'=>'required'
        ]);

        $logoPath = null;

        if ($request->hasFile('logo_file')) {
            $logoPath = $request->file('logo_file')->store('logos','public');
        }

        Etablissement::create([
            ...$request->only('name','type','city','address','phone','email'),
            'logo_path'=>$logoPath
        ]);

        return redirect()->route('admin.etablissements.index')->with('success','Établissement créé.');
    }

    public function edit(Etablissement $etablissement)
    {
        $this->authorize('update', $etablissement);

        return view('admin.etablissements.form', compact('etablissement'));
    }

    public function update(Request $request, Etablissement $etablissement)
    {
        $this->authorize('update', $etablissement);

        $data = $request->only('name','type','city','address','phone','email','is_active');

        if ($request->hasFile('logo_file')) {
            $data['logo_path'] = $request->file('logo_file')->store('logos','public');
        }

        $etablissement->update($data);

        return redirect()->route('admin.etablissements.index')->with('success','Établissement modifié.');
    }

    public function destroy(Etablissement $etablissement)
    {
        $this->authorize('delete', $etablissement);

        $etablissement->delete();

        return redirect()->route('admin.etablissements.index')->with('success','Supprimé.');
    }
}