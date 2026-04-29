<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_periods', function (Blueprint $create) {
            $create->id();
            $create->foreignId('user_id')->constrained()->onDelete('cascade');
            $create->foreignId('budget_template_id')->constrained()->onDelete('cascade');

            $create->date('start_date');
            $create->date('end_date');

            $create->boolean('is_active')->default(false);

            $create->decimal('total_budgeted', 12, 2);

            $create->timestamps();

            $create->index(['user_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_periods');
    }
};
