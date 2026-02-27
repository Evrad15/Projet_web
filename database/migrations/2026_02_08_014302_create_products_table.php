<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Le nom doit être unique pour le rapprochement
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00); // Prix de vente par défaut
            $table->integer('quantity')->default(0); // Stock initial à 0
            $table->integer('alert_threshold')->default(5); // Seuil critique pour les alertes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
