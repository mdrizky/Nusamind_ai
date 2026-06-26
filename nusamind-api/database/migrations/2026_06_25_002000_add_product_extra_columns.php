<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_estimate', 12, 2)->nullable();
            $table->integer('min_stock_alert')->default(5);
            $table->string('unit', 20)->default('pcs');
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('tags')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cost_estimate', 'min_stock_alert', 'unit', 'image_url', 'is_active', 'tags']);
        });
    }
};
