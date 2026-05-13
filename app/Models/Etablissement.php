<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etablissement extends Model
{
    protected $fillable = [
        'name',
        'type',
        'city',
        'address',
        'phone',
        'email',
        'logo_path',
        'is_active',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class);
    }

    public function students(): HasMany
    {
        return $this->users()
            ->whereHas('role', fn($q) => $q->whereIn('name', ['eleve', 'etudiant']));
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path) {
            return asset('storage/' . $this->logo_path);
        }
        return $this->type === 'lycee' ? '🏫' : '🎓';
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim($value);
    }

    public function setTypeAttribute($value)
    {
        $allowed = ['lycee', 'universite'];
        $this->attributes['type'] = in_array($value, $allowed) ? $value : 'lycee';
    }
}
