<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_templates', function (Blueprint $create) {
            $create->id();
            $create->foreignId('user_id')->constrained()->onDelete('cascade');
            $create->string('name'); // Ej: "Presupuesto Mensual Estándar"
            $create->text('description')->nullable();

            // Tipo de periodo para lógica de UI (mensual, trimestral, etc.)
            $create->foreignId('budget_period_type_id')->constrained()->onDelete('cascade');

            // Un campo extra para saber el total proyectado sin sumar detalles cada vez
            $create->decimal('total_estimated_amount', 12, 2)->default(0);

            $create->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_templates');
    }
};
