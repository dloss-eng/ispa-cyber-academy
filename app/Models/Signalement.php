<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Signalement extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'description',
        'suspect_contact',
        'incident_date',
        'ticket_number',
        'status',
        'screenshot_path',
        'ai_category',
        'ai_confidence',
        'admin_notes',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'ai_confidence' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateTicket(): string
    {
        return 'SIG-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }

    public function setTypeAttribute($value)
    {
        $allowed = [
            'sms_frauduleux',
            'phishing_whatsapp',
            'phishing_email',
            'faux_site',
            'arnaque_mobile_money',
            'cyberharcelement',
        ];

        $this->attributes['type'] = in_array($value, $allowed) ? $value : 'autre';
    }

    public function setAiConfidenceAttribute($value)
    {
        $this->attributes['ai_confidence'] = max(0, min(1, (float)$value));
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'sms_frauduleux'      => '📱 SMS Frauduleux',
            'phishing_whatsapp'   => '💬 Phishing WhatsApp',
            'phishing_email'      => '📧 Phishing Email',
            'faux_site'           => '🌐 Faux Site Web',
            'arnaque_mobile_money'=> '💰 Arnaque Mobile Money',
            'cyberharcelement'    => '😢 Cyberharcèlement',
            default               => '❓ Autre',
        };
    }

    public function getScreenshotUrlAttribute(): ?string
    {
        return $this->screenshot_path
            ? asset('storage/' . $this->screenshot_path)
            : null;
    }

    protected static function booted()
    {
        static::creating(function ($signalement) {
            if (empty($signalement->ticket_number)) {
                $signalement->ticket_number = self::generateTicket();
            }
            if (empty($signalement->status)) {
                $signalement->status = 'pending';
            }
        });
    }
}
