<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Création des rôles de base de l'application.
     */
    public function run(): void
    {
        // Création du rôle administrateur
        Role::create([
            'name' => 'Admin',
            'description' => 'Administrateur de la plateforme',
        ]);

        // Création du rôle vendeur
        Role::create([
            'name' => 'Seller',
            'description' => 'Utilisateur qui peut publier des produits',
        ]);

        // Création du rôle client
        Role::create([
            'name' => 'Customer',
            'description' => 'Utilisateur qui peut acheter des produits',
        ]);
    }
}
