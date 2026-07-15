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
        Schema::create('fixed_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_category_id')->constrained();
            $table->string('name');
            $table->text('description')->nullable();
            $table->float('base_amount', 10);
            $table->integer('due_day');
            $table->boolean('is_variable');
            $table->boolean('is_active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_expenses');
    }
};
