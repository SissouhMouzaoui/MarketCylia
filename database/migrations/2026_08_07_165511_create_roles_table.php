<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('roles', function (Blueprint $table) {

        // Identifiant unique du rôle
        $table->id();

        // Nom du rôle : Admin, Seller ou Customer
        // Le nom doit être unique dans la base de données
        $table->string('name', 50)->unique();

        // Description facultative du rôle
        // nullable() permet de laisser ce champ vide
        $table->string('description')->nullable();

        // Dates automatiques de création et de modification
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
