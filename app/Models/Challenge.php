<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Str;

class Challenge extends Model
{
    protected $table = 'ctf_challenges';

    protected $fillable = [
        'title', 'slug', 'description', 'scenario',
        'type', 'flag', 'hints', 'points', 'difficulty',
        'module_id', 'is_published', 'order', 'max_attempts',
    ];

    protected function casts(): array
    {
        return [
            'hints'        => 'array',
            'is_published' => 'boolean',
            'points'       => 'integer',
            'max_attempts' => 'integer',
        ];
    }

    // Relations

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ChallengeAttempt::class);
    }

    // Helpers 

    // Vérifie si un flag soumis est correct (insensible à la casse et aux espaces)
    public function checkFlag(string $submitted): bool
    {
        return strtolower(trim($submitted)) === strtolower(trim($this->flag));
    }

    // Tentatives d'un utilisateur sur ce challenge
    public function userAttempts(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->attempts()->where('user_id', $userId)->get();
    }

    // L'utilisateur a-t-il résolu ce challenge ?
    public function isSolvedBy(int $userId): bool
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('is_correct', true)
            ->exists();
    }

    // Nombre de tentatives restantes (0 = épuisé, null = illimité)
    public function remainingAttempts(int $userId): ?int
    {
        if ($this->max_attempts === 0) return null;
        $used = $this->attempts()->where('user_id', $userId)->count();
        return max(0, $this->max_attempts - $used);
    }

    // Points gagnés selon les indices utilisés
    public function pointsForSolve(int $hintsUsed): int
    {
        $penalty = $hintsUsed * 10; // -10 pts par indice utilisé
        return max(10, $this->points - $penalty);
    }

    // Badge couleur selon la difficulté
    public function difficultyColor(): string
    {
        return match ($this->difficulty) {
            'facile'    => 'success',
            'moyen'     => 'warning',
            'difficile' => 'danger',
            default     => 'secondary',
        };
    }

    // Icône selon le type
    public function typeIcon(): string
    {
        return match ($this->type) {
            'flag_hunt'        => '🚩',
            'textual_analysis' => '🔍',
            default            => '🛡️',
        };
    }

    // Boot 
    protected static function booted(): void
    {
        static::creating(function ($challenge) {
            if (empty($challenge->slug)) {
                $challenge->slug = Str::slug($challenge->title) . '-' . Str::random(4);
            }
        });
    }
}
