<?php
namespace App\Helpers;

/**
 * Sanitiseur HTML par whitelist.
 * Utilisé pour le contenu des leçons (saisi par admin/enseignant).
 * Empêche tout XSS même si un compte privilégié est compromis.
 *
 * Approche : whitelist de balises et d'attributs sûrs.
 * Rejette : <script>, <iframe> (sauf si on en ajoute plus tard avec contrôle),
 * onerror=, onclick=, javascript:, data: URIs, etc.
 */
class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'mark',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'ul', 'ol', 'li',
        'blockquote', 'pre', 'code',
        'a', 'img',
        'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'hr', 'div', 'span',
    ];

    private const ALLOWED_ATTRS = [
        'a' => ['href', 'title'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
        'th' => ['colspan', 'rowspan'],
        'td' => ['colspan', 'rowspan'],
        'div' => ['class'],
        'span' => ['class'],
        'code' => ['class'],
        'pre' => ['class'],
    ];

    public static function clean(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // 1. Supprime tout sauf les balises whitelistées
        $allowedTagsString = '<' . implode('><', self::ALLOWED_TAGS) . '>';
        $html = strip_tags($html, $allowedTagsString);

        // 2. Parse via DOMDocument pour nettoyer les attributs proprement
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        // Wrapper UTF-8 pour DOMDocument qui gère mal l'UTF-8 sans déclaration
        $wrapped = '<?xml encoding="UTF-8"?><div>' . $html . '</div>';
        if (! @$dom->loadHTML($wrapped, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED)) {
            libxml_clear_errors();
            return e($html); // Fallback : on échappe tout
        }
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        // Parcours de tous les éléments
        $nodes = iterator_to_array($xpath->query('//*'));
        foreach ($nodes as $node) {
            if (! $node instanceof \DOMElement) {
                continue;
            }
            $tag = strtolower($node->tagName);
            $allowedAttrs = self::ALLOWED_ATTRS[$tag] ?? [];

            // Liste des attributs à supprimer (on collecte d'abord, on supprime après)
            $toRemove = [];
            foreach ($node->attributes as $attr) {
                $name = strtolower($attr->name);
                $value = $attr->value;

                if (! in_array($name, $allowedAttrs, true)) {
                    $toRemove[] = $name;
                    continue;
                }

                // Vérification stricte des href/src : pas de javascript:, data:, vbscript:
                if (in_array($name, ['href', 'src'], true)) {
                    $cleaned = trim($value);
                    if (preg_match('#^\s*(javascript|data|vbscript|file)\s*:#i', $cleaned)) {
                        $toRemove[] = $name;
                        continue;
                    }
                }
            }
            foreach ($toRemove as $attrName) {
                $node->removeAttribute($attrName);
            }

            // Forcer rel="noopener noreferrer" sur les liens externes
            if ($tag === 'a' && $node->hasAttribute('href')) {
                $node->setAttribute('rel', 'noopener noreferrer');
                $node->setAttribute('target', '_blank');
            }
        }

        // Récupération du HTML nettoyé
        $output = '';
        $body = $dom->getElementsByTagName('div')->item(0);
        if ($body) {
            foreach ($body->childNodes as $child) {
                $output .= $dom->saveHTML($child);
            }
        }

        return $output;
    }
}
