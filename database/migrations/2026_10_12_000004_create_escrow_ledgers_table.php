<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escrow_ledger', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('merchant_id');
            $table->decimal('gross_amount', 12, 2);
            $table->decimal('platform_fee_15pct', 12, 2);
            $table->decimal('merchant_payout_85pct', 12, 2);
            $table->string('payout_status')->default('HELD'); // 'HELD', 'RELEASED_TO_MERCHANT', 'REFUNDED'
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escrow_ledger');
    }
};
