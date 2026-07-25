<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_item_details', function (Blueprint $create) {
            $create->id();
            $create->string('name');
            $create->decimal('base_amount', 12, 2);
            $create->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_item_details');
    }
};
