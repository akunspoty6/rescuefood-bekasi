<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('order_code')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('item_id');
            $table->integer('qty')->default(1);
            $table->decimal('total_amount', 12, 2);
            $table->string('escrow_status')->default('PAYMENT_HELD'); // 'PENDING', 'PAYMENT_HELD', 'VERIFIED_VIA_OTP', 'DISBURSED', 'REFUNDED'
            $table->string('pickup_otp', 10);
            $table->string('pickup_otp_hash');
            $table->string('midtrans_snap_id')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('rescue_items')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
