<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * En-têtes de sécurité HTTP.
 *
 * Mitige : XSS résiduel, clickjacking, MIME sniffing,
 * fuites de Referer, exfiltration de données vers des domaines tiers.
 *
 * NB : la CSP est volontairement permissive pour 'self' + ngrok
 *     (besoin de inline styles vu le Blade). On peut durcir plus tard.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Empêche l'embedding du site dans une iframe (clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Empêche le navigateur de "deviner" le MIME type d'un fichier
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Limite ce que le Referer envoie aux domaines externes
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Bloque les fonctionnalités sensibles du navigateur
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=(), usb=()'
        );

        // Content Security Policy : empêche le chargement de scripts externes non autorisés
        // C'est la défense principale contre le XSS exploité (image 3 — exfiltration vers webhook.site)
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://www.youtube.com https://s.ytimg.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: blob: https:",
            "media-src 'self' https://www.youtube.com",
            "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com",
            "connect-src 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "object-src 'none'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // HSTS : force HTTPS (utile en prod ; ne casse rien en local sur http)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
