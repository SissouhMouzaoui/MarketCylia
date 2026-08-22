<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajouter category_id à la table products.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            /*
            --------------------------------------------------
            Catégorie du produit.
            
            Nullable temporairement afin de ne pas casser
            les produits déjà existants.
            --------------------------------------------------
            */

            $table->foreignId('category_id')
                ->nullable()
                ->after('user_id')
                ->constrained('categories')
                ->nullOnDelete();
        });
    }

    /**
     * Supprimer category_id.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->dropForeign([
                'category_id'
            ]);

            $table->dropColumn(
                'category_id'
            );
        });
    }
};
