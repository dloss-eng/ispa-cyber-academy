<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'read_at',
        'data'
    ];

    protected function casts(): array
    {
        return [
            'data'    => 'array',
            'read_at' => 'datetime',
        ];
    }

    // ============================
    // RELATION
    // ============================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ============================
    // HELPERS
    // ============================

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->update(['read_at' => now()]);
        }
    }

    // ============================
    // STATIC FACTORY
    // ============================

    public static function send(
        int $userId,
        string $type,
        string $title,
        string $message,
        string $icon = '🔔',
        array $data = []
    ): self {
        return self::create([
            'user_id' => $userId,
            'type'    => strtolower($type),
            'title'   => trim($title),
            'message' => trim($message),
            'icon'    => $icon,
            'data'    => array_slice($data, 0, 10),
        ]);
    }
}
