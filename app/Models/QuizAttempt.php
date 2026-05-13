<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'total_points',
        'percentage',
        'passed',
        'started_at',
        'completed_at',
        'answers_data',
        'time_spent_seconds',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'passed'       => 'boolean',
            'answers_data' => 'array',
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function setPercentageAttribute($value)
    {
        $this->attributes['percentage'] = max(0, min(100, (int)$value));
    }

    public function setTimeSpentSecondsAttribute($value)
    {
        $this->attributes['time_spent_seconds'] = max(0, min(7200, (int)$value));
    }

    public function setAnswersDataAttribute($value)
    {
        $this->attributes['answers_data'] = json_encode(array_slice((array)$value, 0, 100));
    }

    public function isPassed(): bool
    {
        return $this->passed;
    }
}
