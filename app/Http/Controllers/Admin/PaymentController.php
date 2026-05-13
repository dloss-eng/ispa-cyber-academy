<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Support\Facades\Cache;

class PaymentController extends Controller
{
    public function index()
    {
        // 🔐 sécurité
        $this->authorize('viewAny', Subscription::class);

        // ============================
        // LISTE
        // ============================

        $subscriptions = Subscription::with('etablissement')
            ->latest()
            ->paginate(20);

        // ============================
        // STATS (CACHE)
        // ============================

        $stats = Cache::remember('admin.payments.stats', 300, function () {
            return [
                'total' => Subscription::count(),

                'active' => Subscription::where('status', 'active')
                    ->where('end_date', '>=', now())
                    ->count(),

                // 🔐 revenu réel (ex: paid seulement)
                'revenue' => Subscription::where('status', 'active')
                    ->whereNotNull('transaction_ref')
                    ->sum('amount'),

                'expired' => Subscription::where('end_date', '<', now())->count(),
            ];
        });

        return view('admin.payments.index', compact(
            'subscriptions',
            'stats'
        ));
    }
}