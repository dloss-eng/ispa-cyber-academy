<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Illuminate\Support\Str;

class Lesson extends Model
{
    protected $fillable = [
        'title',
        'content',
        'video_url',
        'duration_minutes',
        'module_id',
        'slug',
        'order',
        'is_published',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    // ✅ Alias singulier pour compatibilité des vues
    public function quiz(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function studentProgress(): HasMany
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function getIsAccessibleAttribute(): bool
    {
        return $this->is_published && $this->module?->is_published;
    }

    protected static function booted()
    {
        static::creating(function ($lesson) {
            if (empty($lesson->slug)) {
                $lesson->slug = Str::slug($lesson->title) . '-' . Str::random(4);
            }
        });
    }
}
