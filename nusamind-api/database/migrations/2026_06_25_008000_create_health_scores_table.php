<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->integer('total_score');
            $table->integer('financial_score')->nullable();
            $table->integer('marketing_score')->nullable();
            $table->integer('sales_score')->nullable();
            $table->integer('customer_score')->nullable();
            $table->integer('stock_score')->nullable();
            $table->text('breakdown_text')->nullable();
            $table->text('recommendations')->nullable();
            $table->timestamp('scored_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_scores');
    }
};
