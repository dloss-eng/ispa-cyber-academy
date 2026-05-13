<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $fillable = [
        'answer_text',
        'answer_text',
        'is_correct',
        'order',
    ];

    protected $guarded = ['question_id'];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function setAnswerTextAttribute($value)
    {
        $this->attributes['answer_text'] = trim($value);
    }
}
