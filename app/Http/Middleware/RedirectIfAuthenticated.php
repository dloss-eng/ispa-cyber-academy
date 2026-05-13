<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {

            $user = Auth::user();

            $role = $user->getRoleName();

            return match ($role) {
                'admin' => redirect()->route('admin.dashboard'),
                'etablissement' => redirect()->route('etablissement.dashboard'),
                'enseignant' => redirect()->route('enseignant.dashboard'),
                'etudiant' => redirect()->route('dashboard'),
                default => redirect('/'),
            };
        }

        return $next($request);
    }
}
