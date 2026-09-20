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
       Schema::create('investment_applications', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->foreignId('investment_opportunity_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->decimal('amount', 15, 2);

    $table->string('status')->default('pending');

    $table->text('notes')->nullable();

    $table->timestamps();

    $table->unique(['user_id', 'investment_opportunity_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_applications');
    }
};
