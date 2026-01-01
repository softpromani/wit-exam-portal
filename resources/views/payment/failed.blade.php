@extends('admin.includes.master')
@section('content')
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center p-5" style="max-width: 500px;">
            <div class="mb-4">
                <div class="display-1 text-danger mb-3">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h2 class="fw-bold text-dark">Payment Failed</h2>
                <p class="text-muted">Unfortunately, your transaction could not be processed at this time.</p>
            </div>

            @if(isset($transaction) && $transaction->error_message)
                <div class="alert alert-danger rounded-3 mb-4 text-start small">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ $transaction->error_message }}
                </div>
            @endif

            <div class="d-grid gap-2">
                @if(isset($transaction) && $transaction->transactionable_id)
                    <a href="{{ route('student.payment.process', $transaction->transactionable_id) }}"
                        class="btn btn-primary btn-lg rounded-pill shadow-sm">
                        Try Again
                    </a>
                @endif
                <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
                    Back to Dashboard
                </a>
            </div>

            <p class="mt-4 text-muted small">
                If money was debited from your account, it will be refunded within 5-7 working days.
            </p>
        </div>
    </div>
@endsection