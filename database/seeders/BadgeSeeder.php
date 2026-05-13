<?php
namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            // ── Badges existants ──────────────────────────────────
            [
                'name'        => 'Premier Quiz Réussi',
                'slug'        => 'premier-quiz',
                'description' => 'Tu as réussi ton tout premier quiz. Le voyage commence !',
                'icon'        => '🎯',
                'category'    => 'quiz',
                'points_required' => 0,
            ],
            [
                'name'        => 'Expert Phishing',
                'slug'        => 'expert-phishing',
                'description' => 'Module Détection du Phishing complété à 100%. Tu sais détecter les pièges !',
                'icon'        => '🎣',
                'category'    => 'progression',
                'points_required' => 0,
            ],
            [
                'name'        => 'Champion Cybersécurité',
                'slug'        => 'champion-cybersecurite',
                'description' => 'Tous les modules complétés. Tu es un vrai champion de la cybersécurité !',
                'icon'        => '🏆',
                'category'    => 'special',
                'points_required' => 0,
            ],
            [
                'name'        => 'Assidu',
                'slug'        => 'assidu',
                'description' => '10 quiz réussis. La persévérance paie !',
                'icon'        => '⭐',
                'category'    => 'progression',
                'points_required' => 0,
            ],
            [
                'name'        => 'Protecteur Mobile Money',
                'slug'        => 'protecteur-mobile-money',
                'description' => 'Module Sécurité Mobile Money complété. Tu protèges ton argent !',
                'icon'        => '📱',
                'category'    => 'progression',
                'points_required' => 0,
            ],
            [
                'name'        => 'Cyber Détective',
                'slug'        => 'cyber-detective',
                'description' => 'Premier signalement validé. Tu aides à sécuriser la communauté !',
                'icon'        => '🔍',
                'category'    => 'special',
                'points_required' => 0,
            ],
            [
                'name'        => 'CTF Master',
                'slug'        => 'ctf-master',
                'description' => '5 challenges CTF réussis. Tu es un vrai hacker éthique !',
                'icon'        => '🏁',
                'category'    => 'special',
                'points_required' => 0,
            ],

            // ── 4 NOUVEAUX BADGES ─────────────────────────────────

            [
                'name'        => 'Cyber Diplômé',
                'slug'        => 'module-complete',
                'description' => 'Tu as terminé un module à 100% ! Félicitations, tu maîtrises ce sujet.',
                'icon'        => '🎓',
                'category'    => 'progression',
                'points_required' => 0,
            ],
            [
                'name'        => 'Mi-Parcours',
                'slug'        => 'module-mi-parcours',
                'description' => 'Tu as complété la moitié d\'un module. Continue, tu y es presque !',
                'icon'        => '⚡',
                'category'    => 'progression',
                'points_required' => 0,
            ],
            [
                'name'        => 'Quiz Master',
                'slug'        => 'quiz-master',
                'description' => 'Tu as réussi tous les quiz d\'un module. Aucune question ne te résiste !',
                'icon'        => '🏅',
                'category'    => 'quiz',
                'points_required' => 0,
            ],
            [
                'name'        => 'Apprenti Cyber',
                'slug'        => 'quiz-apprenti',
                'description' => 'Tu as réussi la moitié des quiz d\'un module. Tu progresses bien !',
                'icon'        => '📚',
                'category'    => 'quiz',
                'points_required' => 0,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['slug' => $badge['slug']], $badge);
        }

        echo "✅ " . count($badges) . " badges insérés/vérifiés.\n";
    }
}
