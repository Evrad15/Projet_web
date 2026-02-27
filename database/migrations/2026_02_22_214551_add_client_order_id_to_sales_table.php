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
        Schema::table('sales', function (Blueprint $table) {
            // On ajoute la colonne et la clé étrangère (nullable car une vente peut être directe sans commande)
            $table->foreignId('client_order_id')->nullable()->constrained('client_orders')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['client_order_id']);
            $table->dropColumn('client_order_id');
        });
    }
};
