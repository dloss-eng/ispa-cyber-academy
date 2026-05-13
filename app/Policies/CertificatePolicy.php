<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    /**
     * Voir la liste (admin uniquement)
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Vérification centrale
     */
    protected function ownsOrAdmin(User $user, Certificate $certificate): bool
    {
        return $user->id === $certificate->user_id || $user->isAdmin();
    }

    /**
     * Voir un certificat
     */
    public function view(User $user, Certificate $certificate): bool
    {
        return $this->ownsOrAdmin($user, $certificate);
    }

    /**
     * Télécharger
     */
    public function download(User $user, Certificate $certificate): bool
    {
        return $this->ownsOrAdmin($user, $certificate);
    }
}