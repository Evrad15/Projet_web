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
        Schema::create('client_orders', function (Blueprint $table) {
            $table->id();
            // Le client qui commande
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            // Numéro de commande unique (ex: CMD-2024-001)
            $table->string('order_number')->unique();

            // Montant total pour éviter de recalculer à chaque fois
            $table->decimal('total_amount', 15, 2)->default(0);

            // Statuts : pending, processing, completed, cancelled
            $table->string('status')->default('pending');

            // L'employé (commercial) qui prend en charge la commande
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');

            $table->text('notes')->nullable(); // Ex: "Livrer après 16h"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_orders');
    }
};
