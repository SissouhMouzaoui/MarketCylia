<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SellerUserSeeder extends Seeder
{
    /**
     * Crée un compte vendeur pour les tests de MarketCylia.
     */
    public function run(): void
    {
        // Récupération du rôle Seller
        $sellerRole = Role::where('name', 'Seller')->firstOrFail();

        // Création ou mise à jour du compte vendeur
        User::updateOrCreate(
            [
                'email' => 'seller@marketcylia.test',
            ],
            [
                'name' => 'MarketCylia Seller',
                'password' => Hash::make('Seller123!'),
                'role_id' => $sellerRole->id,
            ]
        );
    }
}
