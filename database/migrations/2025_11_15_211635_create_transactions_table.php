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
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('category_id')->index('fk_transactions_transaction_categories1_idx');
            $table->unsignedBigInteger('account_id')->index('fk_transactions_accounts1_idx');
            $table->float('amount', 15);
            $table->text('description');
            $table->date('transaction_date');
            $table->unsignedBigInteger('payment_method_id')->index('fk_transactions_transaction_payment_methods1_idx');
            $table->tinyInteger('is_recurring');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
