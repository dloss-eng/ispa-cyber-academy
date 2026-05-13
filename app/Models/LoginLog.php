<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'successful',
        'created_at',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'successful' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function setIpAddressAttribute($value)
    {
        $this->attributes['ip_address'] = filter_var($value, FILTER_VALIDATE_IP) ? $value : null;
    }

    public function setUserAgentAttribute($value)
    {
        $this->attributes['user_agent'] = substr(trim($value), 0, 500);
    }

    protected static function booted()
    {
        static::creating(function ($log) {
            if (empty($log->created_at)) {
                $log->created_at = now();
            }
        });
    }
}
