<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProgress extends Model
{
    protected $table = 'student_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'status',
        'progress_percent',
        'completed_at',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'completed_at'     => 'datetime',
            'progress_percent' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function setProgressPercentAttribute($value)
    {
        $this->attributes['progress_percent'] = max(0, min(100, (int)$value));
    }

    public function setStatusAttribute($value)
    {
        $allowed = ['not_started', 'in_progress', 'completed'];
        $this->attributes['status'] = in_array($value, $allowed) ? $value : 'not_started';
    }
}
