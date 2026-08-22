<?php 

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Attributs pouvant être remplis lors de la création d'un utilisateur
#[Fillable(['name', 'email', 'password', 'role_id'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable 
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Relation avec le rôle de l'utilisateur.
     * Un utilisateur appartient à un seul rôle.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    /**
 * Relation avec les produits du vendeur.
 *
 * Un utilisateur peut posséder plusieurs produits.
 */
    public function products()
    {
    return $this->hasMany(Product::class);
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
        /**
     * ==========================================================
     * RELATION AVEC LE PANIER
     * ==========================================================
     *
     * Un utilisateur peut avoir plusieurs articles dans son panier.
     */

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
    /**
 * ==========================================================
 * COMMANDES DE L'UTILISATEUR
 * ==========================================================
 */

public function orders(): HasMany
{
    return $this->hasMany(Order::class);
}
}
