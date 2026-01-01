@extends('admin.includes.master') <!-- Using existing layout if possible, otherwise could be plain -->
@section('content')
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center p-5" style="max-width: 500px;">
            <div class="mb-4">
                <div class="display-1 text-success mb-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="fw-bold text-dark">Payment Successful!</h2>
                <p class="text-muted">Your transaction has been completed successfully.</p>
            </div>

            @if(isset($transaction))
                <div class="bg-light rounded-3 p-4 mb-4 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small uppercase">Transaction ID</span>
                        <span class="fw-bold">{{ $transaction->transaction_id }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small uppercase">Amount Paid</span>
                        <span class="fw-bold text-primary">₹{{ $transaction->amount }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small uppercase">Date & Time</span>
                        <span
                            class="fw-bold">{{ $transaction->payment_datetime ? $transaction->payment_datetime->format('d M Y, h:i A') : now()->format('d M Y, h:i A') }}</span>
                    </div>
                </div>
            @endif

            <div class="d-grid gap-2">
                <a href="{{ route('student.dashboard') }}" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                    Go to Dashboard
                </a>
                <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill">
                    <i class="fas fa-print me-2"></i>Print Receipt
                </button>
            </div>
        </div>
    </div>
@endsection