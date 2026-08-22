<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'subtotal',
    ];


    /**
     * ==========================================================
     * COMMANDE
     * ==========================================================
     */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }


    /**
     * ==========================================================
     * PRODUIT
     * ==========================================================
     */

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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
            'quantity' => 'integer',
            'subtotal' => 'decimal:2',
        ];
    }
}
