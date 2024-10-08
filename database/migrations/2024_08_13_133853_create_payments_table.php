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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->morphs('paymentable');
            $table->decimal('total_amount', 10, 2)->default('0.00');
            $table->decimal('paid_amount', 10, 2)->default('0.00');
            $table->decimal('fine_amount', 10, 2)->default('0.00');
            $table->decimal('due_amount', 10, 2)->default('0.00');
            $table->string('payment_status')->default('unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
