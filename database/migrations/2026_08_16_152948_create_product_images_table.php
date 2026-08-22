<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Création de la table product_images.
     *
     * Un produit peut posséder plusieurs images.
     */
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {

            $table->id();

            // Produit auquel appartient l'image
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // Chemin de l'image dans le storage
            $table->string('image');

            // Indique si cette image est l'image principale
            $table->boolean('is_primary')
                ->default(false);

            // Permet de définir l'ordre d'affichage des images
            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();
        });
    }

    /**
     * Suppression de la table product_images.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
