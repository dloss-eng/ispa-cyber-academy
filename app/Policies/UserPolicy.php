<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // Voir liste utilisateurs
    public function viewAny(User $authUser): bool
    {
        return $authUser->isAdmin();
    }

    // Voir un utilisateur
    public function view(User $authUser, User $user): bool
    {
        return $authUser->isAdmin() || $authUser->id === $user->id;
    }

    // Créer utilisateur
    public function create(User $authUser): bool
    {
        return $authUser->isAdmin();
    }

    // Modifier utilisateur
    public function update(User $authUser, User $user): bool
    {
        return $authUser->isAdmin() || $authUser->id === $user->id;
    }

    // Supprimer utilisateur
    public function delete(User $authUser, User $user): bool
    {
        return $authUser->isAdmin() && $authUser->id !== $user->id;
    }
}