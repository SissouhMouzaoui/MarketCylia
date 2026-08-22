<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total',
    ];


    /**
     * ==========================================================
     * UTILISATEUR
     * ==========================================================
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * ==========================================================
     * ARTICLES DE LA COMMANDE
     * ==========================================================
     */

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }


    /**
     * ==========================================================
     * CASTS
     * ==========================================================
     */

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }
}
