<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'image',
        'category',
        'slug',
        'points_required',
    ];

    protected $guarded = [];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_badges')
            ->withPivot('earned_at');
    }

    public function getEarnedCountAttribute(): int
    {
        return $this->users_count ?? $this->users()->count();
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }

    public function setCategoryAttribute($value)
    {
        $this->attributes['category'] = strtolower(trim($value));
    }

    protected static function booted()
    {
        static::creating(function ($badge) {
            if (empty($badge->slug)) {
                $badge->slug = Str::slug($badge->name);
            }
        });
    }
}
