<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('brand_tone')->default('santai');
            $table->string('open_hours')->nullable();
            $table->string('shipping_info')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->text('payment_methods')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['brand_tone', 'open_hours', 'shipping_info', 'whatsapp_number', 'payment_methods']);
        });
    }
};
