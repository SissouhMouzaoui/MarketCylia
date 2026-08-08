<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Crée ou met à jour les rôles principaux de MarketCylia.
     */
    public function run(): void
    {
        // Création ou mise à jour du rôle Administrateur
        Role::updateOrCreate(
            ['name' => 'Admin'],
            ['description' => 'Administrateur de la plateforme']
        );

        // Création ou mise à jour du rôle Vendeur
        Role::updateOrCreate(
            ['name' => 'Seller'],
            ['description' => 'Utilisateur qui peut publier des produits']
        );

        // Création ou mise à jour du rôle Client
        Role::updateOrCreate(
            ['name' => 'Customer'],
            ['description' => 'Utilisateur qui peut acheter des produits']
        );
    }
}
