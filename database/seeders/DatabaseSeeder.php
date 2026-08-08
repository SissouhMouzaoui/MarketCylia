<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
public function run(): void
{
    // Création des rôles de l'application
    $this->call(RoleSeeder::class);

    // Création du compte administrateur de test
    $this->call(AdminUserSeeder::class);
}
}
