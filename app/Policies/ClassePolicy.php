<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Classe;

class ClassePolicy
{
    /**
     * Voir une classe
     */
    public function view(User $user, Classe $classe): bool
    {
        return $user->etablissement_id === $classe->etablissement_id;
    }

    /**
     * Voir toutes les classes
     */
    public function viewAny(User $user): bool
    {
        return $user->etablissement_id !== null;
    }

    /**
     * Créer une classe
     */
    public function create(User $user): bool
    {
        return $user->isEtablissement() || $user->isAdmin();
    }

    /**
     * Modifier une classe
     */
    public function update(User $user, Classe $classe): bool
    {
        return $user->etablissement_id === $classe->etablissement_id;
    }

    /**
     * Supprimer une classe
     */
    public function delete(User $user, Classe $classe): bool
    {
        return $user->etablissement_id === $classe->etablissement_id;
    }
}