<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_items', function (Blueprint $create) {
            $create->id();
            $create->foreignId('budget_template_id')->constrained()->onDelete('cascade');
            $create->foreignId('transaction_category_id')->constrained(); // Referencia a tu tabla de categorías

            // El total presupuestado para esta categoría (ej: 600 para Transporte)
            $create->decimal('category_limit', 12, 2);

            $create->text('notes')->nullable();
            $create->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
