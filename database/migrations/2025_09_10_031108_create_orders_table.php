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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('queue_number')->unique();
            $table->decimal('total_price', 10, 2);
            $table->string('status', 40)->default('pending_payment');
            $table->string('ordering_channel', 40)->default('kiosk');
            $table->string('payment_channel', 40)->nullable();
            $table->string('payment_status', 40)->default('pending');
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
