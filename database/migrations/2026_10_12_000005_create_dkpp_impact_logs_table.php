<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dkpp_impact_logs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->decimal('food_weight_kg', 8, 2);
            $table->decimal('co2e_saved_kg', 8, 2);
            $table->decimal('virtual_water_liter', 10, 2);
            $table->decimal('calories_salvaged', 10, 2);
            $table->decimal('economic_saved_rp', 12, 2);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dkpp_impact_logs');
    }
};
