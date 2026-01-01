@extends('admin.includes.master')
@section('content')
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center p-5" style="max-width: 500px;">
            <div class="mb-4">
                <div class="display-1 text-warning mb-3">
                    <i class="fas fa-clock"></i>
                </div>
                <h2 class="fw-bold text-dark">Payment Pending</h2>
                <p class="text-muted">We are waiting for confirmation from your bank. This might take a few minutes.</p>
            </div>

            <div class="alert alert-warning rounded-3 mb-4 text-start small">
                <i class="fas fa-info-circle me-2"></i>
                Please do not refresh the page or click the back button.
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('student.dashboard') }}" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                    Check Status in Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection