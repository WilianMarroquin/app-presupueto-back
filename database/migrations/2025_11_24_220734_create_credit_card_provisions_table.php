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
        Schema::create('credit_card_provisions', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Relación con el plan padre
            $table->foreignId('installment_plan_id')
                ->constrained('installment_plans')
                ->onDelete('cascade');

            // INFO DEL CALENDARIO
            $table->integer('installment_number'); // Ej: 5 (Indispensable para histórico)
            $table->integer('month');
            $table->integer('year');

            $table->decimal('amount', 10, 2);

            // ESTADO
            // pending: Deuda futura (afecta tu "disponible" virtual)
            // settled: Deuda pagada (ya es histórico)
            $table->enum('status', ['pending', 'settled'])->default('pending');

            // LA VINCULACIÓN
            // Puede ser nulo al inicio. Cuando pagues, aquí guardas el ID del pago real.
            $table->foreignId('transaction_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Evita que el Cron Job cree la cuota 5 dos veces.
            $table->unique(
                ['installment_plan_id', 'installment_number'],
                'cc_provisions_plan_inst_unique' // <--- Este es el apodo corto (30 chars)
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_card_provisions');
    }
};
