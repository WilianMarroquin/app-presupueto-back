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
        Schema::create('installment_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->decimal('total_amount', 12, 2);
            $table->integer('total_installments');
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->date('start_date');

            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_plans');
    }
};
