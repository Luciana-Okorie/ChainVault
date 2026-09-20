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
    Schema::create('investment_opportunities', function (Blueprint $table) {
        $table->id();

        $table->string('title');
        $table->text('description')->nullable();

        $table->decimal('target_amount', 15, 2);
        $table->decimal('minimum_investment', 15, 2);

        $table->decimal('expected_return', 5, 2);
        $table->unsignedInteger('duration_months');

        $table->enum('status', [
            'draft',
            'open',
            'funded',
            'closed',
        ])->default('draft');

        $table->date('start_date')->nullable();
        $table->date('end_date')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_opportunities');
    }
};
