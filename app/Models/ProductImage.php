<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * Attributs pouvant être remplis.
     */
    protected $fillable = [
        'product_id',
        'image',
        'is_primary',
        'sort_order',
    ];

    /**
     * Relation avec le produit.
     *
     * Une image appartient à un seul produit.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Conversion automatique des types.
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
