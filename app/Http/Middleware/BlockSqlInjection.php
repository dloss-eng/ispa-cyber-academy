<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockSqlInjection
{
    /**
     * Patterns suspects à détecter dans les paramètres de requête
     */
    private array $patterns = [
        '/(\%27)|(\')|(\-\-)|(\%23)|(#)/ix',
        '/((\%3D)|(=))[^\n]*((\%27)|(\')|(\-\-)|(\%3B)|(;))/ix',
        '/\w*((\%27)|(\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))/ix',
        '/((\%27)|(\'))union/ix',
        '/exec(\s|\+)+(s|x)p\w+/ix',
        '/UNION\s+SELECT/ix',
        '/INSERT\s+INTO/ix',
        '/SELECT\s+.*\s+FROM/ix',
        '/DROP\s+TABLE/ix',
        '/DELETE\s+FROM/ix',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier tous les paramètres GET et POST
        foreach ($request->all() as $key => $value) {
            if (is_string($value) && $this->isSuspicious($value)) {
                \Log::warning('Tentative injection SQL détectée', [
                    'ip'    => $request->ip(),
                    'url'   => $request->fullUrl(),
                    'param' => $key,
                    'value' => $value,
                ]);
                abort(403, 'Requête non autorisée.');
            }
        }

        // Vérifier l'URL elle-même
        if ($this->isSuspicious($request->fullUrl())) {
            \Log::warning('Tentative injection SQL dans URL', [
                'ip'  => $request->ip(),
                'url' => $request->fullUrl(),
            ]);
            abort(403, 'Requête non autorisée.');
        }

        return $next($request);
    }

    private function isSuspicious(string $value): bool
    {
        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        return false;
    }
}
