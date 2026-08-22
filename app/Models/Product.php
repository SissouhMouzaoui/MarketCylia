<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * ==========================================================
     * ATTRIBUTS REMPLISSABLES
     * ==========================================================
     */

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'is_active',
    ];


    /**
     * ==========================================================
     * RELATION AVEC LE VENDEUR
     * ==========================================================
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * ==========================================================
     * RELATION AVEC LA CATÉGORIE
     * ==========================================================
     *
     * Un produit appartient à une seule catégorie.
     */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    /**
     * ==========================================================
     * RELATION AVEC LES IMAGES
     * ==========================================================
     *
     * Un produit peut posséder plusieurs images.
     */

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
/**
 * ==========================================================
 * RELATION AVEC LES ARTICLES DU PANIER
 * ==========================================================
 *
 * Un produit peut être présent dans plusieurs paniers.
 */

public function cartItems(): HasMany
{
    return $this->hasMany(CartItem::class);
}

    /**
     * ==========================================================
     * CASTS
     * ==========================================================
     */

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

}
