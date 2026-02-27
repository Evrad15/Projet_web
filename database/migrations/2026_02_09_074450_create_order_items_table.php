<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            // Liaison avec la commande parente
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            // On garde le nom du produit (important pour le rapprochement auto)
            $table->string('product_name'); 
            
            // Quantité commandée
            $table->integer('quantity');
            
            // Optionnel : Prix d'achat unitaire au moment de la commande
            $table->decimal('unit_price', 10, 2)->nullable(); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
