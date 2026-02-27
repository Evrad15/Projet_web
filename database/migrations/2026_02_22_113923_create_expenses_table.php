<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');

            // Utilise cette syntaxe précise :
            $table->foreignId('expense_category_id')
                ->constrained('expense_categories') // Vérifie bien que le nom de la table est exact
                ->onDelete('cascade');

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
