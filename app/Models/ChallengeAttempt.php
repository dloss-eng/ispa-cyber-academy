<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeAttempt extends Model
{
    protected $table = 'ctf_attempts';

    protected $fillable = [
        'user_id', 'challenge_id', 'submitted_flag',
        'is_correct', 'hints_used', 'points_earned', 'solved_at',
    ];

    protected function casts(): array
    {
        return [
            'is_correct'   => 'boolean',
            'hints_used'   => 'integer',
            'points_earned'=> 'integer',
            'solved_at'    => 'datetime',
        ];
    }

    // ── Relations ─────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }
}
