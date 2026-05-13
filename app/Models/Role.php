<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [

        'name',
        'display_name',
        'description'
    ];



    // ============================
    // RELATION
    // ============================

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // ============================
    // MUTATORS
    // ============================

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower(trim($value));
    }

    public function setDisplayNameAttribute($value)
    {
        $this->attributes['display_name'] = trim($value);
    }

    // ============================
    // HELPERS
    // ============================

    public function isAdmin(): bool
    {
        return $this->name === 'admin';
    }

    public function isEtablissement(): bool
    {
        return $this->name === 'etablissement';
    }

    public function isEnseignant(): bool
    {
        return $this->name === 'enseignant';
    }

    public function isLearner(): bool
    {
        return in_array($this->name, ['eleve', 'etudiant']);
    }
}