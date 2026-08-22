<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Les champs de cart_items existent déjà.
        // Cette migration ne nécessite aucune modification.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rien à annuler.
    }
};
