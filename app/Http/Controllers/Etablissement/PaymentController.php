<?php

namespace App\Http\Controllers\Etablissement;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // ── Affichage ─────────────────────────────────────

    public function index()
    {
        $etab = Auth::user()->etablissement;

        $subscription = Subscription::where('etablissement_id', $etab->id)
            ->where('status', 'active')
            ->latest('end_date')
            ->first();

        $history = Subscription::where('etablissement_id', $etab->id)
            ->latest()
            ->get();

        return view('etablissement.payments', compact('etab','subscription','history'));
    }

    // ── Souscription ──────────────────────────────────

    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'plan' => 'required|in:basic,premium,enterprise',
            'payment_method' => 'required|in:mtn_momo,orange_money,wave,visa,mastercard',
        ]);

        $etab = Auth::user()->etablissement;

        // 🔐 empêcher double abonnement actif
        $active = Subscription::where('etablissement_id', $etab->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->exists();

        if ($active) {
            return back()->withErrors([
                'subscription' => 'Vous avez déjà un abonnement actif.'
            ]);
        }

        // 🔧 config centralisée
        $plans = [
            'basic' => ['price' => 50000, 'days' => 90],
            'premium' => ['price' => 150000, 'days' => 180],
            'enterprise' => ['price' => 350000, 'days' => 365],
        ];

        $plan = $plans[$data['plan']];

        DB::transaction(function () use ($etab, $data, $plan) {

            Subscription::create([
                'etablissement_id' => $etab->id,
                'plan' => $data['plan'],
                'amount' => $plan['price'],
                'payment_method' => $data['payment_method'],
                'transaction_ref' => $this->generateUniqueRef(),
                'status' => 'pending', // 🔐 IMPORTANT → pas active direct
                'start_date' => now(),
                'end_date' => now()->addDays($plan['days']),
            ]);

        });

        return redirect()
            ->route('etablissement.payments')
            ->with('success', 'Demande d’abonnement envoyée. En attente de validation.');
    }

    // ── Helpers ──────────────────────────────────────

    protected function generateUniqueRef(): string
    {
        do {
            $ref = 'TXN-' . strtoupper(Str::random(10));
        } while (Subscription::where('transaction_ref', $ref)->exists());

        return $ref;
    }
}