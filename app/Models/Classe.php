<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};

class Classe extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'level',
        'year',
        'etablissement_id',
        'enseignant_id',
        'is_active',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'user_id')
            ->withTimestamps();
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }

    public function setLevelAttribute($value)
    {
        $this->attributes['level'] = strtolower(trim($value));
    }

    public function isOwnedBy(User $user): bool
    {
        return $user->isAdmin() || $user->etablissement_id === $this->etablissement_id;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
