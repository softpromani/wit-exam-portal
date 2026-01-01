<?php
// app/Models/Transaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_id',
        'merchant_transaction_id',
        'gateway_transaction_id',
        'consumer_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'gateway',
        'request_data',
        'response_data',
        'gateway_response',
        'hash',
        'customer_name',
        'customer_email',
        'customer_mobile',
        'payment_datetime',
        'error_message',
        'error_code',
        'metadata'
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'amount' => 'decimal:2',
        'payment_datetime' => 'datetime'
    ];

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    // Status update methods
    public function markAsPending($data = [])
    {
        $this->update([
            'status' => 'pending',
            'response_data' => $data
        ]);
    }

    public function markAsSuccess($gatewayResponse)
    {
        $this->update([
            'status' => 'success',
            'gateway_response' => $gatewayResponse,
            'payment_datetime' => now(),
            'gateway_transaction_id' => $gatewayResponse['gateway_txn_id'] ?? null
        ]);
    }

    public function markAsFailed($errorMessage = null, $errorCode = null, $gatewayResponse = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'error_code' => $errorCode,
            'gateway_response' => $gatewayResponse
        ]);
    }

    public function markAsCancelled($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'error_message' => $reason
        ]);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
