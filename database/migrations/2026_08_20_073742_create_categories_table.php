<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Créer la table categories.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {

            $table->id();

            /*
            --------------------------------------------------
            Nom de la catégorie
            Exemple :
            - Vêtements
            - Accessoires
            - Sacs
            - Meubles
            --------------------------------------------------
            */

            $table->string('name')->unique();

            /*
            --------------------------------------------------
            Description facultative
            --------------------------------------------------
            */

            $table->text('description')->nullable();

            /*
            --------------------------------------------------
            Permet à l'administrateur d'activer/désactiver
            une catégorie sans la supprimer.
            --------------------------------------------------
            */

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Supprimer la table categories.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
