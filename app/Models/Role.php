<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description'])]
class Role extends Model
{
    /**
     * Relation avec les utilisateurs.
     * Un rôle peut être associé à plusieurs utilisateurs.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
