<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SignalementController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Signalement::class);

        $signalements = Signalement::with('user')->latest()->paginate(20);

        $stats = Cache::remember('admin.signalements.stats', 300, function () {
            return [
                'total'   => Signalement::count(),
                'nouveau' => Signalement::where('status', 'nouveau')->count(),
                'traite'  => Signalement::where('status', 'traite')->count(),
            ];
        });

        return view('admin.signalements.index', compact('signalements', 'stats'));
    }

    public function show(Signalement $signalement)
    {
        $this->authorize('view', $signalement);
        return view('admin.signalements.show', compact('signalement'));
    }

    public function updateStatus(Request $request, Signalement $signalement)
    {
        $this->authorize('update', $signalement);

        $data = $request->validate([
            'status'      => 'required|in:nouveau,en_cours,traite,rejete',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $signalement->update($data);

        // Invalider le cache des stats
        Cache::forget('admin.signalements.stats');

        \Log::info('Signalement mis à jour', [
            'signalement_id' => $signalement->id,
            'admin_id'       => auth()->id(),
            'new_status'     => $data['status'],
        ]);

        return back()->with('success', 'Statut mis à jour.');
    }
}
