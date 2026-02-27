<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // L'utilisateur qui a fait l'action
            $table->string('type'); // 'initial', 'supply', 'sale', 'correction', 'return'
            $table->integer('quantity'); // Positif (entrée) ou Négatif (sortie)
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_movements');
    }
};
