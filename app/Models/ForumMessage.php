<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class ForumMessage extends Model
{
    protected $fillable = [
        'topic_id',
        'user_id',
        'parent_id',
        'body',
    ];

    protected $guarded = [];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumMessage::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumMessage::class, 'parent_id');
    }

    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = trim(strip_tags($value));
    }

    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}
