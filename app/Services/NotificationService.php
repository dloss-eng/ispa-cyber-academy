<?php
namespace App\Services;

use App\Models\UserNotification;
use App\Models\User;

class NotificationService
{
    // ── Étudiants ─────────────────────────────────────────────────

    public static function welcome(User $u): void
    {
        UserNotification::send($u->id, 'welcome', 'Bienvenue ! 🎉', 'Bienvenue sur ISPA Cyber Academy !', '🛡️');
    }

    public static function quizPassed(User $u, string $q, int $s, int $p): void
    {
        UserNotification::send($u->id, 'quiz', 'Quiz réussi ! ✅', "Score de {$s}% au quiz « {$q} ». +{$p} points !", '📝');
    }

    public static function quizFailed(User $u, string $q, int $s): void
    {
        UserNotification::send($u->id, 'quiz', 'Quiz non réussi', "Score de {$s}% au quiz « {$q} ». Réessayez !", '❌');
    }

    public static function badgeEarned(User $u, string $n, string $i): void
    {
        UserNotification::send($u->id, 'badge', 'Nouveau badge ! 🏅', "Badge « {$n} » obtenu !", $i);
    }

    public static function certificateEarned(User $u, string $m): void
    {
        UserNotification::send($u->id, 'certificate', 'Certificat obtenu ! 🏆', "Certificat pour « {$m} » prêt.", '📜');
    }

    public static function lessonCompleted(User $u, string $l): void
    {
        UserNotification::send($u->id, 'progress', 'Leçon terminée ! ✅', "« {$l} » terminée. +10 points !", '📖');
    }

    // ── Nouveau module publié ──────────────────────────────────────

    /**
     * Notifie tous les apprenants concernés par le niveau du module.
     * lycee → élèves | universite → étudiants | tous → les deux
     */
    public static function newModulePublished(string $moduleTitle, string $level): void
    {
        $roles = match($level) {
            'lycee'      => ['eleve'],
            'universite' => ['etudiant'],
            default      => ['eleve', 'etudiant'],
        };

        $levelLabel = match($level) {
            'lycee'      => '🏫 Lycée',
            'universite' => '🎓 Université',
            default      => '🌐 Tous niveaux',
        };

        User::whereHas('role', fn($q) => $q->whereIn('name', $roles))
            ->where('is_active', true)
            ->each(function (User $user) use ($moduleTitle, $levelLabel) {
                UserNotification::send(
                    $user->id,
                    'new_module',
                    '📚 Nouveau module disponible !',
                    "Le module « {$moduleTitle} » ({$levelLabel}) vient d'être publié. Découvrez-le maintenant !",
                    '📚'
                );
            });
    }

    /**
     * Notifie tous les établissements qu'un nouveau module est disponible.
     */
    public static function newModuleForEtablissement(string $moduleTitle, string $level): void
    {
        $levelLabel = match($level) {
            'lycee'      => '🏫 Lycée',
            'universite' => '🎓 Université',
            default      => '🌐 Tous niveaux',
        };

        User::whereHas('role', fn($q) => $q->where('name', 'etablissement'))
            ->where('is_active', true)
            ->each(function (User $etab) use ($moduleTitle, $levelLabel) {
                UserNotification::send(
                    $etab->id,
                    'new_module',
                    '📚 Nouveau module publié',
                    "Un nouveau module « {$moduleTitle} » ({$levelLabel}) est disponible pour vos apprenants.",
                    '📚'
                );
            });
    }

    // ── Admin ──────────────────────────────────────────────────────

    public static function notifyAdmins(string $type, string $title, string $message, string $icon = '🔔'): void
    {
        $admins = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->get();
        foreach ($admins as $admin) {
            UserNotification::send($admin->id, $type, $title, $message, $icon);
        }
    }

    public static function contactMessage(string $prenom, string $nom, string $email, string $sujet, string $message): void
    {
        self::notifyAdmins(
            'contact',
            '📩 Nouveau message de contact',
            "De : {$prenom} {$nom} ({$email})\nSujet : {$sujet}\n\n{$message}",
            '📩'
        );
    }

    public static function newUserRegistered(string $name, string $email): void
    {
        self::notifyAdmins(
            'user',
            '👤 Nouvel utilisateur inscrit',
            "{$name} ({$email}) vient de s'inscrire.",
            '👤'
        );
    }

    public static function newSignalement(string $title, string $userName): void
    {
        self::notifyAdmins(
            'signalement',
            '🚨 Nouveau signalement',
            "{$userName} a soumis un signalement : « {$title} ».",
            '🚨'
        );
    }

    public static function newPayment(string $userName, string $plan): void
    {
        self::notifyAdmins(
            'payment',
            '💰 Nouveau paiement',
            "{$userName} a souscrit au plan « {$plan} ».",
            '💰'
        );
    }
}
