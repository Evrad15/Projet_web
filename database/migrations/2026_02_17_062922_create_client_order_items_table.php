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
        Schema::create('client_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_order_id')->constrained('client_orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');

            $table->integer('quantity');

            // On enregistre le prix unitaire AU MOMENT de la commande 
            // (important si le prix du produit change plus tard en stock)
            $table->decimal('unit_price', 15, 2);

            // Sous-total pour cette ligne (quantité * unit_price)
            $table->decimal('subtotal', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_order_items');
    }
};
