@extends('admin.includes.master')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-history text-danger mr-2"></i> Student Promotion History & Disaster Recovery
            </h1>
            <p class="text-muted small mb-0">
                Complete audit records of all semester promotion batches. If an accidental or incorrect promotion occurs, use <strong>Disaster Recovery Revert</strong> to restore students.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.student.list') }}" class="btn btn-sm btn-primary shadow-sm font-weight-bold">
                <i class="fas fa-users mr-1"></i> Student List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Metrics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Batches
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $promotions->total() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Promoted
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-success">
                                {{ \App\Models\Promotion::where('status', 'promoted')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Reverted Batches
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-warning">
                                {{ \App\Models\Promotion::where('status', 'reverted')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-undo-alt fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Students Promoted
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Promotion::sum('students_count') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clipboard-list mr-1"></i> Promotion Audit Log ({{ $promotions->total() }} records)
            </h6>
        </div>
        <div class="card-body">
            @if($promotions->count() === 0)
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3 d-block"></i>
                    <h5 class="text-gray-700 font-weight-bold">No Promotion Records Yet</h5>
                    <p class="text-muted">When you promote students in the Student List, automated audit logs and disaster recovery snapshots will appear here.</p>
                    <a href="{{ route('admin.student.list') }}" class="btn btn-sm btn-primary">
                        Go to Student List
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Batch No</th>
                                <th>Date & Time</th>
                                <th>Promoted By</th>
                                <th>Students</th>
                                <th>Semesters (From &rarr; To)</th>
                                <th>Status</th>
                                <th>Notes / Filter</th>
                                <th style="width: 170px;" class="text-center">Disaster Recovery</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($promotions as $promo)
                                <tr>
                                    <td>{{ ($promotions->currentPage() - 1) * $promotions->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <span class="badge badge-dark px-2 py-1 font-weight-bold" style="font-size: 85%;">
                                            {{ $promo->batch_no }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $promo->created_at->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $promo->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold">{{ $promo->user?->name ?? 'Admin' }}</div>
                                        <small class="text-muted">{{ $promo->user?->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-pill badge-primary px-2 py-1 font-weight-bold" style="font-size: 90%;">
                                            <i class="fas fa-user-graduate mr-1"></i> {{ $promo->students_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary px-2 py-1">{{ $promo->from_semester_name ?? 'N/A' }}</span>
                                        <i class="fas fa-arrow-right mx-1 text-primary"></i>
                                        <span class="badge badge-success px-2 py-1">{{ $promo->to_semester_name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @if($promo->status === 'reverted')
                                            <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold">
                                                <i class="fas fa-undo mr-1"></i> Reverted
                                            </span>
                                            <small class="d-block text-muted">
                                                {{ $promo->reverted_at ? $promo->reverted_at->format('d M Y, h:i A') : '' }}
                                            </small>
                                            @if($promo->revertedByUser)
                                                <small class="d-block text-muted">by {{ $promo->revertedByUser->name }}</small>
                                            @endif
                                        @else
                                            <span class="badge badge-success px-2 py-1 font-weight-bold">
                                                <i class="fas fa-check-circle mr-1"></i> Promoted
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="d-block text-gray-800">{{ $promo->notes ?? 'Promoted via admin' }}</small>
                                        @if(is_array($promo->filter_criteria) && isset($promo->filter_criteria['mode']) && $promo->filter_criteria['mode'] === 'selected_ids')
                                            <small class="badge badge-light border">Selected {{ $promo->filter_criteria['count'] ?? '' }} IDs</small>
                                        @elseif(is_array($promo->filter_criteria))
                                            <small class="badge badge-info">Filtered Cohort</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.student.promotion-details', $promo->id) }}" class="btn btn-sm btn-info mb-1" title="View Students in Batch">
                                            <i class="fas fa-eye"></i> Details
                                        </a>
                                        @if($promo->status === 'promoted')
                                            <button type="button" class="btn btn-sm btn-danger mb-1 revert-trigger-btn"
                                                data-id="{{ $promo->id }}"
                                                data-batch="{{ $promo->batch_no }}"
                                                data-count="{{ $promo->students_count }}"
                                                data-from="{{ $promo->from_semester_name }}"
                                                data-to="{{ $promo->to_semester_name }}"
                                                title="Revert this promotion">
                                                <i class="fas fa-undo-alt mr-1"></i> Revert
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-secondary mb-1" disabled title="Already reverted">
                                                <i class="fas fa-check mr-1"></i> Reverted
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-between pt-3 border-top">
                    <div class="text-muted small">
                        Showing {{ $promotions->firstItem() ?? 0 }} to {{ $promotions->lastItem() ?? 0 }} of {{ $promotions->total() }} batches
                    </div>
                    <div>
                        {{ $promotions->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Disaster Recovery Revert Modal -->
<div class="modal fade" id="revertModal" tabindex="-1" role="dialog" aria-labelledby="revertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form id="revert-form" method="POST" action="">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="revertModalLabel">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Disaster Recovery Revert
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger mb-3">
                        <div class="font-weight-bold mb-1">
                            <i class="fas fa-radiation mr-1"></i> You are about to initiate disaster recovery rollback!
                        </div>
                        Batch <span id="revert-batch-label" class="badge badge-dark"></span> with
                        <strong><span id="revert-count-label"></span> student(s)</strong> will be moved from
                        <span id="revert-to-label" class="badge badge-success"></span> back to
                        <span id="revert-from-label" class="badge badge-secondary"></span>.
                    </div>
                    <p class="text-muted small mb-0">
                        This atomic rollback updates all students' current semester in the database back to the snapshot taken at promotion time.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold" id="confirm-revert-submit-btn">
                        <i class="fas fa-undo mr-1"></i> Yes, Revert This Promotion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script_section')
<script>
    document.querySelectorAll('.revert-trigger-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const promoId = this.dataset.id;
            const batch = this.dataset.batch;
            const count = this.dataset.count;
            const from = this.dataset.from;
            const to = this.dataset.to;

            document.getElementById('revert-batch-label').innerText = batch;
            document.getElementById('revert-count-label').innerText = count;
            document.getElementById('revert-from-label').innerText = from;
            document.getElementById('revert-to-label').innerText = to;

            const form = document.getElementById('revert-form');
            form.action = "{{ url('admin/promotion-revert') }}/" + promoId;

            $('#revertModal').modal('show');
        });
    });

    const revertForm = document.getElementById('revert-form');
    if (revertForm) {
        revertForm.addEventListener('submit', function() {
            const btn = document.getElementById('confirm-revert-submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Reverting...';
        });
    }
</script>
@endsection
