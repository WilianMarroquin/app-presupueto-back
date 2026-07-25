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
        Schema::create('budget_tamplate_item_has_details', function (Blueprint $table) {
            $table->foreignId('budget_item_id')->constrained('budget_items');
            $table->string('model_type', 255)->nullable();
            $table->integer('model_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_tamplate_item_has_details');
    }
};
