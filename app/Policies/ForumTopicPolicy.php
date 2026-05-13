<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ForumTopic;

class ForumTopicPolicy
{
    /**
     * Voir la liste des sujets
     */
    public function viewAny(User $user): bool
    {
        return true; // forum accessible à tous les utilisateurs connectés
    }

    /**
     * Voir un sujet
     */
    public function view(User $user, ForumTopic $topic): bool
    {
        return true; // tous peuvent voir
    }

    /**
     * Créer un sujet
     */
    public function create(User $user): bool
    {
        return $user->isLearner() || $user->isEnseignant() || $user->isAdmin();
    }

    /**
     * Modifier un sujet
     */
    public function update(User $user, ForumTopic $topic): bool
    {
        return $user->isAdmin() || $topic->user_id === $user->id;
    }

    /**
     * Supprimer un sujet
     */
    public function delete(User $user, ForumTopic $topic): bool
    {
        return $user->isAdmin() || $topic->user_id === $user->id;
    }

    /**
     * Verrouiller / déverrouiller
     */
    public function lock(User $user, ForumTopic $topic): bool
    {
        return $user->isAdmin();
    }
}