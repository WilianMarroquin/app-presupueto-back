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
        Schema::table('budgets', function (Blueprint $table) {
            $table->foreign(['period_types_id'], 'fk_budgets_budget_period_types1')->references(['id'])->on('budget_period_types')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['category_id'], 'fk_budgets_transaction_categories1')->references(['id'])->on('transaction_categories')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropForeign('fk_budgets_budget_period_types1');
            $table->dropForeign('fk_budgets_transaction_categories1');
        });
    }
};
