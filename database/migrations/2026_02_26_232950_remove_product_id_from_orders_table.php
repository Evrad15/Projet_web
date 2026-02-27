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
        if (!Schema::hasColumn('orders', 'product_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            // Important : on supprime d'abord la clé étrangère, puis la colonne
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'product_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            // Pour pouvoir annuler (rollback), on recrée la colonne
            $table->foreignId('product_id')->nullable()->constrained();
        });
    }
};
