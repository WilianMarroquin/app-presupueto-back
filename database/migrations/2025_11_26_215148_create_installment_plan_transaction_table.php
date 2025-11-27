<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('installment_plan_transaction', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('installment_plan_id')
                ->constrained()
                ->onDelete('cascade');

            $table->integer('installment_number');
            $table->decimal('amount', 12, 2)->nullable();

            $table->timestamps();

            $table->unique(['installment_plan_id', 'installment_number'], 'unique_plan_quota');
        });
    }

    public function down()
    {
        Schema::dropIfExists('installment_plan_transaction');
    }
};
