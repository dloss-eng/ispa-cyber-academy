<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Etablissement;

class EtablissementPolicy
{
    /**
     * Voir la liste (admin uniquement)
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Créer
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Voir un établissement
     */
    public function view(User $user, Etablissement $etablissement): bool
    {
        return $user->isAdmin() || $user->etablissement_id === $etablissement->id;
    }

    /**
     * Modifier
     */
    public function update(User $user, Etablissement $etablissement): bool
    {
        return $user->isAdmin() || $user->etablissement_id === $etablissement->id;
    }

    /**
     * Supprimer
     */
    public function delete(User $user, Etablissement $etablissement): bool
    {
        return $user->isAdmin();
    }
}