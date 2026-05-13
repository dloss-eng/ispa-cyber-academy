<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;

class SubscriptionPolicy
{
    /**
     * Voir la liste des abonnements (admin seulement)
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Voir un abonnement spécifique
     */
    public function view(User $user, Subscription $subscription): bool
    {
        // Admin → accès total
        if ($user->isAdmin()) {
            return true;
        }

        // Établissement → seulement ses abonnements
        return $user->isEtablissement()
            && $user->etablissement_id === $subscription->etablissement_id;
    }

    /**
     * Créer un abonnement
     */
    public function create(User $user): bool
    {
        // Admin ou établissement peuvent créer
        return $user->isAdmin() || $user->isEtablissement();
    }

    /**
     * Modifier un abonnement
     */
    public function update(User $user, Subscription $subscription): bool
    {
        // Admin → peut tout modifier
        if ($user->isAdmin()) {
            return true;
        }

        // Établissement → seulement ses abonnements
        return $user->isEtablissement()
            && $user->etablissement_id === $subscription->etablissement_id;
    }

    /**
     * Supprimer un abonnement
     */
    public function delete(User $user, Subscription $subscription): bool
    {
        // 🔐 uniquement admin
        return $user->isAdmin();
    }

    /**
     * Restaurer (si soft delete)
     */
    public function restore(User $user, Subscription $subscription): bool
    {
        return $user->isAdmin();
    }

    /**
     * Suppression définitive
     */
    public function forceDelete(User $user, Subscription $subscription): bool
    {
        return $user->isAdmin();
    }
}