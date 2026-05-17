<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockSqlInjection
{
    /**
     * Patterns dangereux UNIQUEMENT dans les paramètres GET/POST
     * (pas dans l'URL elle-même pour éviter les faux positifs)
     */
    private array $patterns = [
        '/(\bunion\b.+\bselect\b)/i',
        '/(\bselect\b.+\bfrom\b.+\bwhere\b)/i',
        '/(\bdrop\s+table\b)/i',
        '/(\bdelete\s+from\b)/i',
        '/(\binsert\s+into\b)/i',
        '/(\bexec\s*\()/i',
        '/(\bexecute\s*\()/i',
        "/(';.*--)/",
        '/(\bor\b\s+[\'"]?\d+[\'"]?\s*=\s*[\'"]?\d+[\'"]?\s*--)/i',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Ne vérifier QUE les paramètres GET/POST — pas l'URL entière
        foreach ($request->query() as $key => $value) {
            if (is_string($value) && $this->isSuspicious($value)) {
                \Illuminate\Support\Facades\Log::warning('SQL Injection tentative (GET)', [
                    'ip'    => $request->ip(),
                    'url'   => $request->fullUrl(),
                    'param' => $key,
                    'value' => substr($value, 0, 200),
                ]);
                abort(403, 'Requête non autorisée.');
            }
        }

        foreach ($request->post() as $key => $value) {
            if (is_string($value) && $this->isSuspicious($value)) {
                \Illuminate\Support\Facades\Log::warning('SQL Injection tentative (POST)', [
                    'ip'    => $request->ip(),
                    'url'   => $request->fullUrl(),
                    'param' => $key,
                    'value' => substr($value, 0, 200),
                ]);
                abort(403, 'Requête non autorisée.');
            }
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
