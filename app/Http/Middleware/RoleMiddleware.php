<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // 🔐 Vérifier utilisateur connecté
        if (!$user) {
            abort(403, 'Utilisateur non authentifié.');
        }

        // 🔥 Support "role:admin,etudiant"
        $allowedRoles = collect($roles)
            ->flatMap(fn ($role) => explode(',', $role))
            ->map(fn ($role) => trim($role))
            ->toArray();

        // 🔎 Récupérer rôle propre
        $userRole = $user->getRoleName();

        // 🚨 Vérification sécurisée
        if (!$userRole || !in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}