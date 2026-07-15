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
        Schema::create('fixed_expenses_period_paymets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_period_id')->constrained();
            $table->foreignId('transaction_id')->constrained();
            $table->foreignId('fixed_expense_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_expenses_period_paymets');
    }
};
