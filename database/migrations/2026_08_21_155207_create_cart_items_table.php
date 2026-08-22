<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ==========================================================
     * CRÉATION DE LA TABLE CART_ITEMS
     * ==========================================================
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {

            $table->id();


            /*
            ======================================================
            UTILISATEUR
            ======================================================
            */

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();


            /*
            ======================================================
            PRODUIT
            ======================================================
            */

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();


            /*
            ======================================================
            QUANTITÉ
            ======================================================
            */

            $table->unsignedInteger('quantity')
                ->default(1);


            /*
            ======================================================
            EMPÊCHER LES DOUBLONS
            ======================================================
            
            Un utilisateur ne peut avoir qu'une seule ligne
            pour un même produit.
            */

            $table->unique([
                'user_id',
                'product_id'
            ]);


            $table->timestamps();
        });
    }


    /**
     * ==========================================================
     * SUPPRESSION DE LA TABLE
     * ==========================================================
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
