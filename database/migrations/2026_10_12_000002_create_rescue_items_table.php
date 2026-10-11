<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rescue_items', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('merchant_id');
            $table->string('title');
            $table->string('item_type'); // 'Mystery Bag' / 'Specific Item'
            $table->decimal('original_price', 12, 2);
            $table->integer('discount_pct');
            $table->decimal('rescue_price', 12, 2);
            $table->integer('stock_qty')->default(0);
            $table->string('pickup_start', 10);
            $table->string('pickup_end', 10);
            $table->decimal('est_weight_kg', 8, 2)->default(0.80);
            $table->string('status')->default('ACTIVE_FLASH'); // 'ACTIVE_FLASH', 'SOLD_OUT', 'EXPIRED'
            $table->timestamps();

            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rescue_items');
    }
};
