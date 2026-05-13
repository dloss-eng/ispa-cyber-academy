<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BadgeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Badge::class);

        $badges = Badge::withCount('users')->paginate(20);

        return view('admin.badges.index', compact('badges'));
    }

    public function create()
    {
        $this->authorize('create', Badge::class);

        return view('admin.badges.form');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Badge::class);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string|max:50',
            'category' => 'required|in:quiz,progression,special',
            'points_required' => 'required|integer|min:0|max:10000',
        ]);

        // 🔐 slug unique
        $data['slug'] = Str::slug($data['name']) . '-' . uniqid();

        Badge::create($data);

        return redirect()
            ->route('admin.badges.index')
            ->with('success', 'Badge créé.');
    }

    public function edit(Badge $badge)
    {
        $this->authorize('update', $badge);

        return view('admin.badges.form', compact('badge'));
    }

    public function update(Request $request, Badge $badge)
    {
        $this->authorize('update', $badge);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|string|max:50',
            'category' => 'required|in:quiz,progression,special',
            'points_required' => 'required|integer|min:0|max:10000',
        ]);

        $badge->update($data);

        return redirect()
            ->route('admin.badges.index')
            ->with('success', 'Badge modifié.');
    }

    public function destroy(Badge $badge)
    {
        $this->authorize('delete', $badge);

        // 🔐 sécurité : vérifier utilisation
        if ($badge->users()->exists()) {
            return back()->withErrors([
                'badge' => 'Impossible de supprimer un badge déjà attribué.'
            ]);
        }

        $badge->delete();

        return redirect()
            ->route('admin.badges.index')
            ->with('success', 'Badge supprimé.');
    }

    public function holders(Badge $badge)
    {
        $this->authorize('view', $badge);

        $users = $badge->users()
            ->with('role')
            ->orderByPivot('earned_at', 'desc')
            ->paginate(20);

        return view('admin.badges.holders', compact('badge', 'users'));
    }
}