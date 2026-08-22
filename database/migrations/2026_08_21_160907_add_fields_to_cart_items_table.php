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
        // Les colonnes existent déjà dans cart_items.
        // Aucune modification supplémentaire n'est nécessaire.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rien à annuler.
    }
};
