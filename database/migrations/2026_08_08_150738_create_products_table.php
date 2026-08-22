<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Création de la table des produits.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            // Identifiant unique du produit
            $table->id();

            // Identifiant du vendeur propriétaire du produit
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Informations principales du produit
            $table->string('name');
            $table->text('description')->nullable();

            // Prix et quantité disponible
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock')->default(0);

            // Image du produit (optionnelle)
            $table->string('image')->nullable();

            // Permet au vendeur d'activer ou désactiver son produit
            $table->boolean('is_active')->default(true);

            // Dates de création et de modification
            $table->timestamps();
        });
    }

    /**
     * Suppression de la table des produits.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
