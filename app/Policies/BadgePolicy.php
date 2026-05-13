<?php

namespace App\Policies;

use App\Models\Badge;
use App\Models\User;

class BadgePolicy
{
    // Voir tous les badges
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    // Voir un badge
    public function view(User $user, Badge $badge): bool
    {
        return $user->isAdmin();
    }

    // Créer un badge
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    // Modifier un badge
    public function update(User $user, Badge $badge): bool
    {
        return $user->isAdmin();
    }

    // Supprimer un badge
    public function delete(User $user, Badge $badge): bool
    {
        return $user->isAdmin();
    }
}