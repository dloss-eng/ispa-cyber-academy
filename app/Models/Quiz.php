<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Quiz extends Model
{
    protected $fillable = [
        'lesson_id',
        'module_id',   // ✅ Option B — quiz lié au module
        'title',
        'description',
        'time_limit_minutes',
        'passing_score',
        'max_attempts',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    // ── Relations ──────────────────────────────────────────────
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // ── Accesseurs ─────────────────────────────────────────────
    public function getTotalPointsAttribute(): int
    {
        return $this->questions_sum_points ?? $this->questions()->sum('points');
    }

    /**
     * Quiz accessible si publié et son parent (leçon OU module) est publié
     */
    public function getIsAccessibleAttribute(): bool
    {
        if (!$this->is_published) return false;

        // Quiz de module
        if ($this->module_id) {
            return (bool) $this->module?->is_published;
        }

        // Quiz de leçon
        return $this->lesson?->is_published
            && $this->lesson?->module?->is_published;
    }

    public function remainingAttempts(User $user): int
    {
        $attempts = $this->attempts()
            ->where('user_id', $user->id)
            ->count();

        return max(0, $this->max_attempts - $attempts);
    }
}
