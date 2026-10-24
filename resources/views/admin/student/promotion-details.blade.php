@extends('admin.includes.master')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-layer-group text-primary mr-2"></i> Batch {{ $promotion->batch_no }} Details
            </h1>
            <p class="text-muted small mb-0">
                Showing individual students promoted in this audit snapshot.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.student.promotion-history') }}" class="btn btn-sm btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left mr-1"></i> Back to History
            </a>
            @if($promotion->status === 'promoted')
                <button type="button" class="btn btn-sm btn-danger shadow-sm font-weight-bold" data-toggle="modal" data-target="#revertModal">
                    <i class="fas fa-undo mr-1"></i> Revert Batch
                </button>
            @endif
        </div>
    </div>

    <!-- Batch Summary Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle mr-1"></i> Batch Information
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="text-xs font-weight-bold text-uppercase text-muted">Batch Number</div>
                    <div class="h6 font-weight-bold text-dark mt-1">{{ $promotion->batch_no }}</div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="text-xs font-weight-bold text-uppercase text-muted">Promotion Date</div>
                    <div class="h6 font-weight-bold text-dark mt-1">
                        {{ $promotion->created_at->format('d M Y, h:i A') }}
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="text-xs font-weight-bold text-uppercase text-muted">Promoted By</div>
                    <div class="h6 font-weight-bold text-dark mt-1">
                        {{ $promotion->user?->name ?? 'Admin' }} ({{ $promotion->user?->email }})
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="text-xs font-weight-bold text-uppercase text-muted">Status</div>
                    <div class="mt-1">
                        @if($promotion->status === 'reverted')
                            <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold">
                                <i class="fas fa-undo mr-1"></i> Reverted on {{ $promotion->reverted_at?->format('d M Y, h:i A') }}
                            </span>
                        @else
                            <span class="badge badge-success px-2 py-1 font-weight-bold">
                                <i class="fas fa-check-circle mr-1"></i> Active (Promoted)
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="text-xs font-weight-bold text-uppercase text-muted">Total Students</div>
                    <div class="h6 font-weight-bold text-primary mt-1">
                        {{ $promotion->students_count }} students
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="text-xs font-weight-bold text-uppercase text-muted">Semesters Transition</div>
                    <div class="h6 font-weight-bold text-dark mt-1">
                        <span class="badge badge-secondary">{{ $promotion->from_semester_name }}</span>
                        &rarr;
                        <span class="badge badge-success">{{ $promotion->to_semester_name }}</span>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 mb-3">
                    <div class="text-xs font-weight-bold text-uppercase text-muted">Notes / Scope</div>
                    <div class="small text-muted mt-1">
                        {{ $promotion->notes ?? 'Promoted via admin panel' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users mr-1"></i> Students in Batch ({{ $promotionStudents->total() }})
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Reg. No</th>
                            <th>University Roll No</th>
                            <th>Student Name</th>
                            <th>Branch</th>
                            <th>Session</th>
                            <th>From Sem</th>
                            <th>To Sem</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promotionStudents as $ps)
                            <tr>
                                <td>{{ ($promotionStudents->currentPage() - 1) * $promotionStudents->perPage() + $loop->iteration }}</td>
                                <td><strong>{{ $ps->student?->registration_no ?? 'N/A' }}</strong></td>
                                <td>{{ $ps->student?->university_roll_no ?? 'N/A' }}</td>
                                <td>{{ $ps->student?->student_name ?? 'N/A' }}</td>
                                <td>{{ $ps->student?->branch?->name ?? 'N/A' }}</td>
                                <td>{{ $ps->student?->admission_session?->session_name ?? 'N/A' }}</td>
                                <td><span class="badge badge-secondary px-2 py-1">{{ $ps->fromSemester?->semester_name ?? ('Sem ' . $ps->from_semester_id) }}</span></td>
                                <td><span class="badge badge-success px-2 py-1">{{ $ps->toSemester?->semester_name ?? ('Sem ' . $ps->to_semester_id) }}</span></td>
                                <td>
                                    @if($ps->status === 'reverted')
                                        <span class="badge badge-warning text-dark px-2 py-1">
                                            <i class="fas fa-undo mr-1"></i> Reverted
                                        </span>
                                    @else
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-check mr-1"></i> Promoted
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-between pt-3 border-top">
                <div class="text-muted small">
                    Showing {{ $promotionStudents->firstItem() ?? 0 }} to {{ $promotionStudents->lastItem() ?? 0 }} of {{ $promotionStudents->total() }} students
                </div>
                <div>
                    {{ $promotionStudents->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

@if($promotion->status === 'promoted')
<!-- Revert Modal -->
<div class="modal fade" id="revertModal" tabindex="-1" role="dialog" aria-labelledby="revertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <form method="POST" action="{{ route('admin.student.promotion-revert', $promotion->id) }}">
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
                            <i class="fas fa-radiation mr-1"></i> Rollback Warning:
                        </div>
                        Batch <strong>{{ $promotion->batch_no }}</strong> with <strong>{{ $promotion->students_count }} student(s)</strong> will be reverted back to
                        <strong>{{ $promotion->from_semester_name }}</strong>.
                    </div>
                    <p class="text-muted small mb-0">
                        This atomic rollback updates all students' current semester in the database back to the snapshot taken at promotion time.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm font-weight-bold">
                        <i class="fas fa-undo mr-1"></i> Yes, Revert This Promotion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
