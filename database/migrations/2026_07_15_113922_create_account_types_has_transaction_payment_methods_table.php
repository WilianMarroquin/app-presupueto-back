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
        Schema::create('account_types_has_payment_methods', function (Blueprint $table) {
            // Creamos las columnas puras primero
            $table->foreignId('account_type_id');
            $table->foreignId('transaction_payment_method_id');

            // Definimos las llaves foráneas con un nombre corto personalizado (Manual)
            $table->foreign('account_type_id', 'fk_acc_type_pivot')
                ->references('id')
                ->on('account_types')
                ->onDelete('cascade'); // Opcional: por si borras un tipo de cuenta

            $table->foreign('transaction_payment_method_id', 'fk_pay_method_pivot')
                ->references('id')
                ->on('transaction_payment_methods')
                ->onDelete('cascade'); // Opcional: por si borras un método

            // Tu índice único se mantiene perfecto porque ya tiene un nombre corto
            $table->unique(['account_type_id', 'transaction_payment_method_id'], 'type_method_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_types_has_transaction_payment_methods');
    }
};
