<?php

namespace App\Policies;

use App\Models\Signalement;
use App\Models\User;

class SignalementPolicy
{
    /**
     * Voir la liste des signalements
     */
    public function viewAny(User $user): bool
    {
        // Admin → voit tout
        if ($user->isAdmin()) {
            return true;
        }

        // Utilisateur → peut voir ses propres signalements (liste filtrée côté controller)
        return true;
    }

    /**
     * Voir un signalement spécifique
     */
    public function view(User $user, Signalement $signalement): bool
    {
        // Admin → accès total
        if ($user->isAdmin()) {
            return true;
        }

        // Utilisateur → seulement ses signalements
        return $user->id === $signalement->user_id;
    }

    /**
     * Créer un signalement
     */
    public function create(User $user): bool
    {
        // Tous les utilisateurs connectés peuvent signaler
        return true;
    }

    /**
     * Modifier un signalement
     */
    public function update(User $user, Signalement $signalement): bool
    {
        // Admin uniquement
        return $user->isAdmin();
    }

    /**
     * Supprimer un signalement
     */
    public function delete(User $user, Signalement $signalement): bool
    {
        // 🔐 uniquement admin
        return $user->isAdmin();
    }

    /**
     * Restaurer (si soft delete)
     */
    public function restore(User $user, Signalement $signalement): bool
    {
        return $user->isAdmin();
    }

    /**
     * Suppression définitive
     */
    public function forceDelete(User $user, Signalement $signalement): bool
    {
        return $user->isAdmin();
    }
}