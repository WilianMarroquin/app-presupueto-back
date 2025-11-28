<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('credit_card_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('account_id')
                ->unique()
                ->constrained('accounts')
                ->onDelete('cascade');

            $table->string('alias', 50)->nullable(); // Ej: Visa Oro
            $table->string('network', 20); // visa, mastercard, amex
            $table->string('color', 7)->default('#000000'); // Hex del plástico
            $table->string('last_4', 4); // Solo para mostrar "**** 1234"
            $table->float('credit_limit', 8, 2)->default(0); // Límite de crédito

            $table->integer('cutoff_day'); // Día de corte (1-31)
            $table->integer('payment_day'); // Día límite de pago (1-31)

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_card_details');
    }
};
