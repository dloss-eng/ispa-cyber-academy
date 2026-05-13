<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class ForumTopic extends Model
{
    protected $fillable = [
        'user_id',
        'module_id',
        'title',
        'body',
        'is_pinned',
        'is_locked',
        'views_count',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_pinned'   => 'boolean',
            'is_locked'   => 'boolean',
            'views_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ForumMessage::class, 'topic_id');
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = trim(strip_tags($value));
    }

    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = trim(strip_tags($value));
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isLocked(): bool
    {
        return $this->is_locked;
    }
}
