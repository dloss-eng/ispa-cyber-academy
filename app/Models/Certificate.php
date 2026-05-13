<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'module_id',
        'certificate_number',
        'file_path',
        'final_score',
        'issued_at',
        'qr_code',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'issued_at'   => 'datetime',
            'final_score' => 'integer',
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

    public function setFinalScoreAttribute($value)
    {
        $this->attributes['final_score'] = max(0, min(100, (int)$value));
    }

    public static function generateNumber(): string
    {
        return 'ISPA-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(6)));
    }

    protected static function booted()
    {
        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = self::generateNumber();
            }
            if (empty($certificate->issued_at)) {
                $certificate->issued_at = now();
            }
        });
    }
}
