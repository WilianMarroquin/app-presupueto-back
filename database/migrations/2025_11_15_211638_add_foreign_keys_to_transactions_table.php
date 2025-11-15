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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign(['account_id'], 'fk_transactions_accounts1')->references(['id'])->on('accounts')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['category_id'], 'fk_transactions_transaction_categories1')->references(['id'])->on('transaction_categories')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['payment_method_id'], 'fk_transactions_transaction_payment_methods1')->references(['id'])->on('transaction_payment_methods')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('fk_transactions_accounts1');
            $table->dropForeign('fk_transactions_transaction_categories1');
            $table->dropForeign('fk_transactions_transaction_payment_methods1');
        });
    }
};
