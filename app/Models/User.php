<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasMany};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // 🔐 Constantes rôles
    public const ROLE_ADMIN = 'admin';
    public const ROLE_ETABLISSEMENT = 'etablissement';
    public const ROLE_ENSEIGNANT = 'enseignant';
    public const ROLE_ETUDIANT = 'etudiant';

    protected $fillable = [
            'name',
            'email',
            'password',
            'phone',
            'avatar',
            'bio',
            'role_id',          // ✅ AJOUTE
            'etablissement_id',
            'is_active'
            ];



    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'two_factor_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ];
    }

    // ============================
    // RELATIONS
    // ============================

    public function role(): BelongsTo { return $this->belongsTo(Role::class); }
    
    public function etablissement(): BelongsTo 
    { return $this->belongsTo(Etablissement::class); }
    
    public function classes()
    {
        return $this->belongsToMany(Classe::class, 'class_student', 'user_id', 'class_id');
    }
    
    public function quizAttempts(): HasMany 
    { return $this->hasMany(QuizAttempt::class); }
    
    public function progress(): HasMany 
    { return $this->hasMany(StudentProgress::class); }
    
    public function badges(): BelongsToMany 
    { return $this->belongsToMany(Badge::class, 'student_badges')->withPivot('earned_at'); }
    
    public function certificates(): HasMany 
    { return $this->hasMany(Certificate::class); }
    
    public function forumTopics(): HasMany 
    { return $this->hasMany(ForumTopic::class); }
    
    public function loginLogs(): HasMany 
    { return $this->hasMany(LoginLog::class); }
    
    public function userNotifications(): HasMany 
    { return $this->hasMany(UserNotification::class)->latest(); }
    
    public function signalements(): HasMany { return $this->hasMany(Signalement::class); }

    public function unreadNotificationsCount(): int
    {
        return $this->userNotifications()
            ->whereNull('read_at')
            ->count();
    }

    // ============================
    // RÔLES
    // ============================

    public function getRoleName(): ?string
    {
        return $this->relationLoaded('role')
            ? $this->role?->name
            : $this->role()->value('name'); // ⚡ évite N+1
    }

    public function hasRole(string $role): bool
    {
        return $this->getRoleName() === $role;
    }

    public function isAdmin(): bool { return $this->hasRole(self::ROLE_ADMIN); }
    
    public function isEtablissement(): bool { return $this->hasRole(self::ROLE_ETABLISSEMENT); }
    
    public function isEnseignant(): bool { return $this->hasRole(self::ROLE_ENSEIGNANT); }

    public function isEtudiant(): bool { return $this->hasRole(self::ROLE_ETUDIANT); }

    public function isLearner(): bool
    {
        // ✅ inclut 'eleve' pour cohérence avec RoleMiddleware
        return in_array($this->getRoleName(), [self::ROLE_ETUDIANT, 'eleve']);
    }

    public function allowedModuleLevels(): array
    {
        return match ($this->getRoleName()) {
            'eleve'     => ['lycee', 'tous'],
            'etudiant'  => ['universite', 'tous'],
            default     => ['lycee', 'universite', 'tous'],
        };
    }

    public function moduleLevelLabel(): string
    {
        return match ($this->getRoleName()) {
            'eleve'    => 'Lycée',
            'etudiant' => 'Université',
            default    => 'Tous niveaux',
        };
    }

    // ============================
    // GAMIFICATION
    // ============================

    public function addPoints(int $pts): void
    {
        $pts = max(0, min(1000, $pts)); // 🔐 limite

        $this->increment('points', $pts);
        $this->updateLevel();
    }

    public function updateLevel(): void
    {
        $newLevel = match(true) {
            $this->points >= 5000 => 10,
            $this->points >= 3000 => 8,
            $this->points >= 2000 => 6,
            $this->points >= 1000 => 4,
            $this->points >= 500 => 3,
            $this->points >= 100 => 2,
            default => 1,
        };

        if ($newLevel !== $this->level) {
            $this->update(['level' => $newLevel]);
        }
    }

    // ============================
    // AVATAR
    // ============================

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && !str_contains($this->avatar, '..')) {
            return asset('storage/avatars/' . $this->avatar);
        }

        return asset('images/default-avatar.png');
    }

    // ============================
    // DISPLAY
    // ============================

    public function getRoleDisplayAttribute(): string
    {
        return match($this->getRoleName()) {
            self::ROLE_ADMIN         => 'Administrateur',
            self::ROLE_ETABLISSEMENT => 'Établissement',
            self::ROLE_ENSEIGNANT    => 'Enseignant',
            self::ROLE_ETUDIANT      => 'Étudiant',
            'eleve'                  => 'Élève',        
            default                  => 'Utilisateur',
        };
    }
}