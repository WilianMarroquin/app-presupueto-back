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
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreign(['currency_id'], 'fk_accounts_account_currencys1')->references(['id'])->on('account_currencys')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['type_id'], 'fk_accounts_account_types1')->references(['id'])->on('account_types')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign('fk_accounts_account_currencys1');
            $table->dropForeign('fk_accounts_account_types1');
        });
    }
};
