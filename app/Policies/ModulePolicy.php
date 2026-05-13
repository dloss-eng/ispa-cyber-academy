<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Module;

class ModulePolicy
{
    /**
     * Voir liste modules
     */
    public function viewAny(User $user): bool
    {
        return true; // OK
    }

    /**
     * Voir un module
     */
    public function view(User $user, Module $module): bool
    {
        return $module->is_published || $user->isAdmin();
    }

    /**
     * Créer module
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Modifier module
     */
    public function update(User $user, Module $module): bool
    {
        return $user->isAdmin();
    }

    /**
     * Supprimer module
     */
    public function delete(User $user, Module $module): bool
    {
        return $user->isAdmin();
    }
}