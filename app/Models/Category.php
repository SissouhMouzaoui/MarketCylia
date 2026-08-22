<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    /**
     * ==========================================================
     * ATTRIBUTS REMPLISSABLES
     * ==========================================================
     */

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];


    /**
     * ==========================================================
     * RELATION AVEC LES PRODUITS
     * ==========================================================
     *
     * Une catégorie peut contenir plusieurs produits.
     */

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }


    /**
     * ==========================================================
     * CASTS
     * ==========================================================
     */

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
