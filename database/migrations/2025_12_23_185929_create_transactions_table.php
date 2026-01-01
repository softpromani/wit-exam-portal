<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->morphs('transactionable'); // For polymorphic relationship
            $table->string('transaction_id')->unique();
            $table->string('merchant_transaction_id')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->string('consumer_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('payment_method')->nullable();
            $table->enum('status', ['initiated', 'pending', 'processing', 'success', 'failed', 'cancelled', 'refunded'])->default('initiated');
            $table->string('gateway')->default('worldline');
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->json('gateway_response')->nullable();
            $table->string('hash')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_mobile')->nullable();
            $table->datetime('payment_datetime')->nullable();
            $table->string('error_message')->nullable();
            $table->string('error_code')->nullable();
            $table->json('metadata')->nullable(); // For additional data
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['gateway_transaction_id']);
            $table->index(['consumer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
