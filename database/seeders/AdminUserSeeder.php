<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Crée un compte administrateur pour MarketCylia.
     */
    public function run(): void
    {
        // Récupération du rôle Admin déjà présent dans la base de données
        $adminRole = Role::where('name', 'Admin')->firstOrFail();

        // Création ou récupération du compte administrateur
        User::updateOrCreate(
            [
                'email' => 'admin@marketcylia.test',
            ],
            [
                'name' => 'MarketCylia Admin',
                'password' => Hash::make('Admin123!'),
                'role_id' => $adminRole->id,
            ]
        );
    }
}
