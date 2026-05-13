<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'etablissement_id',
        'plan',
        'payment_method',
        'amount',
        'transaction_ref',
        'status',
        'start_date',
        'end_date',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
            'amount'     => 'float',
        ];
    }

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->end_date
            && $this->end_date->isFuture();
    }

    public function daysRemaining(): int
    {
        return $this->end_date
            ? max(0, now()->diffInDays($this->end_date, false))
            : 0;
    }

    public function setPlanAttribute($value)
    {
        $allowed = ['basic', 'premium', 'enterprise'];
        $this->attributes['plan'] = in_array($value, $allowed) ? $value : 'basic';
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = max(0, (float)$value);
    }

    public function setPaymentMethodAttribute($value)
    {
        $allowed = ['mtn_momo', 'orange_money', 'wave', 'visa', 'mastercard'];
        $this->attributes['payment_method'] = in_array($value, $allowed) ? $value : 'mtn_momo';
    }

    public function getPlanLabelAttribute(): string
    {
        return match ($this->plan) {
            'basic'      => '🥉 Basic',
            'premium'    => '🥈 Premium',
            'enterprise' => '🥇 Enterprise',
            default      => '—',
        };
    }

    public function getPaymentLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'mtn_momo'     => '📱 MTN MoMo',
            'orange_money' => '🟠 Orange Money',
            'wave'         => '🌊 Wave',
            'visa'         => '💳 Visa',
            'mastercard'   => '💳 Mastercard',
            default        => '—',
        };
    }

    protected static function booted()
    {
        static::creating(function ($sub) {
            if (empty($sub->transaction_ref)) {
                $sub->transaction_ref = strtoupper(bin2hex(random_bytes(6)));
            }
            if (empty($sub->status)) {
                $sub->status = 'pending';
            }
            if (empty($sub->start_date)) {
                $sub->start_date = now();
            }
            if (empty($sub->end_date)) {
                $sub->end_date = now()->addDays(30);
            }
        });
    }
}
