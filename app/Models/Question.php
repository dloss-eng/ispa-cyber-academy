<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Question extends Model
{
    protected $fillable = [
        'question_text',
        'explanation',
        'points',
        'type',
        'order',
    ];

    protected $guarded = ['quiz_id'];

    // ============================
    // RELATIONS
    // ============================

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class)->orderBy('order');
    }

    public function correctAnswers(): HasMany
    {
        return $this->answers()->where('is_correct', true);
    }

    // ============================
    // MUTATORS (SÉCURITÉ)
    // ============================

    public function setQuestionTextAttribute($value)
    {
        $this->attributes['question_text'] = trim(strip_tags($value));
    }

    public function setExplanationAttribute($value)
    {
        $this->attributes['explanation'] = trim(strip_tags($value));
    }

    public function setPointsAttribute($value)
    {
        $this->attributes['points'] = max(1, min(100, (int)$value));
    }

    public function setTypeAttribute($value)
    {
        $allowed = ['qcm', 'choix_multiple', 'vrai_faux'];

        $this->attributes['type'] = in_array($value, $allowed)
            ? $value
            : 'qcm';
    }

    // ============================
    // HELPERS
    // ============================

    public function isMultipleChoice(): bool
    {
        return $this->type === 'choix_multiple';
    }
}
