@extends('admin.includes.master')

@section('style_area')
<style>
    .loading {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" style="margin:auto; background:none; display:block;" width="20px" height="20px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="50" cy="50" fill="none" stroke="%2300f" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform></circle></svg>');
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 20px 20px;
    }
    .text-decoration-underline {
        text-decoration: underline !important;
    }
</style>
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Student List</h1>
        <div>
            <a href="{{ route('admin.student.promotion-history') }}" class="btn btn-sm btn-outline-danger shadow-sm mr-2 font-weight-bold">
                <i class="fas fa-history mr-1"></i> Promotion History & Disaster Recovery
            </a>
            <a href="{{ route('admin.student.index') }}" class="btn btn-sm btn-outline-primary shadow-sm mr-2 font-weight-bold">
                <i class="fas fa-file-excel mr-1"></i> Bulk Import
            </a>
            <a href="{{ route('admin.student.create') }}" class="btn btn-sm btn-primary shadow-sm font-weight-bold">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Add Student
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Overall Profile & Fee Summary Cards -->
    <div class="row mb-4">
        <!-- Total Students -->
        <div class="col-xl-3 col-md-6 mb-3">
            <a href="{{ route('admin.student.list', ['fetch' => 1]) }}" style="text-decoration: none;">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Students
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $globalTotalStudents ?? \App\Models\Student::count() }}
                                </div>
                                <small class="text-muted">Click to view all</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Completed Profiles -->
        <div class="col-xl-3 col-md-6 mb-3">
            <a href="{{ route('admin.student.list', ['is_profile' => 1]) }}" style="text-decoration: none;">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Profile Completed
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-success">
                                    {{ $globalCompletedStudents ?? \App\Models\Student::where('is_profile', 1)->count() }}
                                </div>
                                <small class="text-muted">Click to filter completed</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-check fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Incomplete Profiles -->
        <div class="col-xl-3 col-md-6 mb-3">
            <a href="{{ route('admin.student.list', ['is_profile' => 0]) }}" style="text-decoration: none;">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Profile Incomplete
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-danger">
                                    {{ $globalIncompleteStudents ?? \App\Models\Student::where('is_profile', 0)->orWhereNull('is_profile')->count() }}
                                </div>
                                <small class="text-muted">Click to filter incomplete</small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-clock fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Student Fee Collected / Total Paid -->
        <div class="col-xl-3 col-md-6 mb-3">
            <a href="{{ route('admin.student.list', ['fee_status' => 'paid']) }}" style="text-decoration: none;">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Fee Collected / Total
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₹{{ number_format($globalCollectedFee ?? 0, 2) }}
                                </div>
                                <small class="text-info font-weight-bold">
                                    <i class="fas fa-user-check mr-1"></i>{{ $globalPaidStudents ?? 0 }}/{{ $globalTotalStudents ?? 0 }} Students Paid
                                </small>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-rupee-sign fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <form action="{{ route('admin.student.list') }}" method="GET">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter mr-1"></i> Filter Students
                </h6>
                @if(request()->has('is_profile') || request()->has('fee_status') || request()->has('branch_id') || request()->has('semester_id') || request()->has('admission_session_id'))
                    <span class="badge badge-info">Filters Active</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 col-sm-6 mb-3">
                        <label for="branch_id" class="font-weight-bold">Branch</label>
                        <select class="form-control" name="branch_id" id="branch_id">
                            <option value="">All Branches</option>
                            @foreach($courses as $course)
                                <optgroup label="{{ $course->name }}">
                                    @foreach($course->branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <label for="semester_id" class="font-weight-bold">Semester</label>
                        <select class="form-control" name="semester_id" id="semester_id">
                            <option value="">All Semesters</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                    {{ $sem->semester_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <label for="admission_session_id" class="font-weight-bold">Admission Session</label>
                        <select class="form-control" name="admission_session_id" id="admission_session_id">
                            <option value="">All Sessions</option>
                            @foreach($admission_sessions as $ads)
                                <option value="{{ $ads->id }}" {{ request('admission_session_id') == $ads->id ? 'selected' : '' }}>
                                    {{ $ads->session_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <label for="is_profile" class="font-weight-bold">Profile Status</label>
                        <select class="form-control" name="is_profile" id="is_profile">
                            <option value="">All Statuses</option>
                            <option value="1" {{ (string)request('is_profile') === '1' ? 'selected' : '' }}>
                                Completed
                            </option>
                            <option value="0" {{ (string)request('is_profile') === '0' ? 'selected' : '' }}>
                                Incomplete
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <label for="fee_status" class="font-weight-bold">Fee Status</label>
                        <select class="form-control" name="fee_status" id="fee_status">
                            <option value="">All Fee Statuses</option>
                            <option value="paid" {{ request('fee_status') === 'paid' ? 'selected' : '' }}>
                                Fee Paid
                            </option>
                            <option value="unpaid" {{ request('fee_status') === 'unpaid' ? 'selected' : '' }}>
                                Fee Unpaid
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-3">
                        <label for="per_page" class="font-weight-bold">Per Page</label>
                        <select class="form-control" name="per_page" id="per_page">
                            <option value="25" {{ request('per_page', 50) == 25 ? 'selected' : '' }}>25 students</option>
                            <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50 students</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 students</option>
                            <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200 students</option>
                            <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500 students</option>
                        </select>
                    </div>

                    <div class="col-12 mt-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search mr-1"></i> Fetch Students
                        </button>
                        <a href="{{ route('admin.student.list') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-undo mr-1"></i> Reset Filters
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Student Table Section -->
    @isset($students)
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list mr-1"></i> Students (Showing {{ $students->firstItem() ?? 0 }}-{{ $students->lastItem() ?? 0 }} of {{ $filteredCount ?? $students->total() }})
                </h6>
                <div class="mt-2 mt-sm-0">
                    <span class="badge badge-success px-2 py-1 mr-1">
                        <i class="fas fa-check-circle"></i> {{ $filteredCompleted ?? 0 }} Profile Completed
                    </span>
                    <span class="badge badge-danger px-2 py-1 mr-1">
                        <i class="fas fa-times-circle"></i> {{ $filteredIncomplete ?? 0 }} Profile Incomplete
                    </span>
                    <span class="badge badge-info px-2 py-1">
                        <i class="fas fa-rupee-sign"></i> ₹{{ number_format($filteredCollectedFee ?? 0, 2) }} Collected ({{ $filteredPaidStudents ?? 0 }}/{{ $filteredCount ?? $students->total() }} Paid)
                    </span>
                </div>
            </div>
            <div class="card-body">
                @if($students->count() == 0)
                    <div class="alert alert-info text-center py-4 my-2">
                        <i class="fas fa-info-circle fa-2x mb-2 text-info d-block"></i>
                        No students found matching the selected filter criteria.
                    </div>
                @else
                    <form id="select-students-form" method="POST" action="{{ route('admin.student-promote') }}">
                        @csrf
                        <input type="hidden" name="promote_all_filtered" id="promote_all_filtered" value="0">
                        <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                        <input type="hidden" name="semester_id" value="{{ request('semester_id') }}">
                        <input type="hidden" name="admission_session_id" value="{{ request('admission_session_id') }}">
                        <input type="hidden" name="is_profile" value="{{ request('is_profile') }}">
                        <input type="hidden" name="fee_status" value="{{ request('fee_status') }}">
                        <input type="hidden" name="course_id" value="{{ request('course_id') }}">
                        <input type="hidden" name="student_name" value="{{ request('student_name') }}">
                        <input type="hidden" name="registration_no" value="{{ request('registration_no') }}">
                        <input type="hidden" name="university_roll_no" value="{{ request('university_roll_no') }}">
                        <input type="hidden" name="registration_type" value="{{ request('registration_type') }}">
                        <input type="hidden" name="notes" id="promotion-notes" value="">

                        <!-- Selection & Disaster Recovery Banner for Pagination -->
                        <div id="selection-banner" class="alert alert-info py-2 px-3 mb-3 d-none align-items-center justify-content-between flex-wrap" style="border-left: 4px solid #36b9cc;">
                            <div>
                                <span id="page-selection-text">
                                    All <strong><span id="page-selected-count">{{ $students->count() }}</span></strong> students on this page are selected.
                                </span>
                                @if($students->total() > $students->count())
                                    <a href="javascript:void(0)" id="select-all-filtered-btn" class="font-weight-bold ml-2 text-primary text-decoration-underline">
                                        Select all {{ $students->total() }} students matching this filter
                                    </a>
                                @endif
                                <span id="all-filtered-selected-text" class="d-none font-weight-bold text-success ml-2">
                                    <i class="fas fa-check-double mr-1"></i> All {{ $students->total() }} students matching current filter are selected for promotion!
                                </span>
                            </div>
                            <div class="mt-1 mt-sm-0">
                                <a href="javascript:void(0)" id="clear-selection-btn" class="text-danger small font-weight-bold">
                                    <i class="fas fa-times-circle mr-1"></i> Clear Selection
                                </a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="students-table">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">
                                            <input type="checkbox" id="select-all" title="Select all on this page">
                                        </th>
                                        <th style="width: 50px;">Sr No</th>
                                        <th>Registration No</th>
                                        <th>University Roll No</th>
                                        <th>Name</th>
                                        <th>Branch</th>
                                        <th>Curr. Sem</th>
                                        <th>Adm. Sem</th>
                                        <th>Admission Session</th>
                                        <th>Profile Status</th>
                                        <th>Fee Collected</th>
                                        <th style="width: 90px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $st)
                                        @php
                                            $studentPaidFee = $st->examForms->sum(function($form) {
                                                return ($form->payment && $form->payment->payment_status === 'paid') ? (float)$form->payment->paid_amount : 0;
                                            });
                                            $studentTotalForms = $st->examForms->count();
                                            $studentPaidForms = $st->examForms->filter(function($form) {
                                                return ($form->payment && $form->payment->payment_status === 'paid') || $form->payment_status === 'done';
                                            })->count();
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="select-item" name="selected_students[]" value="{{$st->id}}">
                                            </td>
                                            <td>{{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}</td>
                                            <td><strong>{{$st->registration_no}}</strong></td>
                                            <td>
                                                <input type="text" value="{{ $st->university_roll_no }}" data-id="{{ $st->id }}"
                                                    class="form-control form-control-sm roll-no-class" />
                                                <small class="text-danger error-message"></small>
                                            </td>
                                            <td>{{$st->student_name}}</td>
                                            <td>{{$st->branch?->name}}</td>
                                            <td><span class="badge badge-info px-2 py-1">{{$st->semester?->semester_name}}</span></td>
                                            <td>{{$st->admission_semester?->semester_name}}</td>
                                            <td>{{$st->admission_session?->session_name}}</td>
                                            <td class="text-center">
                                                @if($st->is_profile == 1)
                                                    <span class="badge badge-success px-2 py-1" title="Profile completed">
                                                        <i class="fas fa-check-circle mr-1"></i> Completed
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger px-2 py-1" title="Profile not yet submitted">
                                                        <i class="fas fa-times-circle mr-1"></i> Incomplete
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($studentPaidForms > 0)
                                                    <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 85%;">
                                                        <i class="fas fa-check-circle mr-1"></i> ₹{{ number_format($studentPaidFee, 2) }}
                                                    </span>
                                                    <small class="d-block text-muted">{{ $studentPaidForms }}/{{ $studentTotalForms }} forms paid</small>
                                                @elseif($studentTotalForms > 0)
                                                    <span class="badge badge-warning text-dark px-2 py-1" style="font-size: 85%;">
                                                        <i class="fas fa-clock mr-1"></i> ₹0.00
                                                    </span>
                                                    <small class="d-block text-muted">0/{{ $studentTotalForms }} forms paid</small>
                                                @else
                                                    <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 85%;">
                                                        No Forms
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('reciept', $st->id) }}" class="btn btn-sm btn-primary"
                                                    title="View Receipt">
                                                    <i class="fa fa-file-invoice"></i>
                                                </a>
                                                <a href="{{ route('admin.student.edit', $st->id) }}" class="btn btn-sm btn-info"
                                                    title="Edit Student">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <th colspan="12" class="p-2" style="width: 100%; display: table-cell;">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap w-100">
                                                <div class="d-flex align-items-center">
                                                    <button class="btn btn-danger btn-sm shadow-sm font-weight-bold" type="button" id="promote-btn">
                                                        <i class="fas fa-level-up-alt mr-1"></i> Promote Next Sem
                                                    </button>
                                                    <span id="promote-summary-badge" class="badge badge-light border ml-2 px-2 py-1">
                                                        0 students selected
                                                    </span>
                                                </div>
                                                <div class="small text-muted mt-2 mt-sm-0">
                                                    <i class="fas fa-shield-alt text-success mr-1"></i> Protected: Snapshot created in Promotion History for instant disaster recovery
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </form>

                    <!-- Pagination Links -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between pt-3 border-top">
                        <div class="text-muted small mb-2 mb-md-0">
                            Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} students
                        </div>
                        <div>
                            {{ $students->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endisset

    <!-- Promote Confirmation Modal -->
    <div class="modal fade" id="promoteModal" tabindex="-1" role="dialog" aria-labelledby="promoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title font-weight-bold" id="promoteModalLabel">
                        <i class="fas fa-level-up-alt mr-2"></i> Confirm Student Promotion
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        <span id="modal-promotion-desc"></span>
                    </div>
                    <div class="form-group">
                        <label for="modal-notes-input" class="font-weight-bold">Promotion Note (Optional):</label>
                        <input type="text" id="modal-notes-input" class="form-control" placeholder="e.g. Regular promotion 2026 Batch">
                    </div>
                    <div class="p-3 bg-light rounded border text-muted small">
                        <div class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-shield-alt text-success mr-1"></i> Disaster Recovery Protection:
                        </div>
                        A full snapshot of all student semester assignments will be saved under <strong>Promotion History</strong>. If any disaster happens or an incorrect promotion occurs, you can revert all affected students with 1 click.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-sm font-weight-bold" id="confirm-promote-btn">
                        <i class="fas fa-check mr-1"></i> Confirm & Promote Now
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script_section')
    <script>
        document.querySelectorAll('.roll-no-class').forEach(function (input) {
            input.addEventListener('blur', function () {
                const value = this.value;
                const studentId = this.dataset.id;
                const errorEl = this.nextElementSibling;

                this.classList.add('loading');
                this.classList.remove('border-success', 'border-danger');
                if (errorEl) errorEl.innerText = '';

                fetch('/admin/student/update-roll-no', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id: studentId,
                        university_roll_no: value
                    })
                })
                    .then(async response => {
                        this.classList.remove('loading');
                        if (response.ok) {
                            this.classList.add('border-success');
                        } else if (response.status === 422) {
                            const data = await response.json();
                            this.classList.add('border-danger');
                            if (data.errors && data.errors.university_roll_no && errorEl) {
                                errorEl.innerText = data.errors.university_roll_no[0];
                            }
                        } else {
                            this.classList.add('border-danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.classList.remove('loading');
                        this.classList.add('border-danger');
                        if (errorEl) errorEl.innerText = 'Something went wrong.';
                    });
            });
        });

        // Dual-Mode Selection & Promotion Logic
        const selectAll = document.getElementById('select-all');
        const selectItems = document.querySelectorAll('.select-item');
        const selectionBanner = document.getElementById('selection-banner');
        const pageSelectionText = document.getElementById('page-selection-text');
        const selectAllFilteredBtn = document.getElementById('select-all-filtered-btn');
        const allFilteredSelectedText = document.getElementById('all-filtered-selected-text');
        const clearSelectionBtn = document.getElementById('clear-selection-btn');
        const promoteAllFilteredInput = document.getElementById('promote_all_filtered');
        const promoteSummaryBadge = document.getElementById('promote-summary-badge');
        const totalMatchingStudents = {{ isset($students) ? $students->total() : 0 }};

        function updateSelectionState() {
            const checkedCount = document.querySelectorAll('.select-item:checked').length;
            const totalPageItems = selectItems.length;

            if (promoteAllFilteredInput.value === '1') {
                if (promoteSummaryBadge) {
                    promoteSummaryBadge.innerText = 'All ' + totalMatchingStudents + ' filtered students selected';
                    promoteSummaryBadge.className = 'badge badge-primary border ml-2 px-2 py-1';
                }
                return;
            }

            if (promoteSummaryBadge) {
                promoteSummaryBadge.innerText = checkedCount + ' student' + (checkedCount === 1 ? '' : 's') + ' selected on page';
                promoteSummaryBadge.className = checkedCount > 0 ? 'badge badge-success border ml-2 px-2 py-1' : 'badge badge-light border ml-2 px-2 py-1';
            }

            if (checkedCount === totalPageItems && totalPageItems > 0) {
                if (selectAll) selectAll.checked = true;
                if (selectionBanner) {
                    selectionBanner.classList.remove('d-none');
                    selectionBanner.classList.add('d-flex');
                }
            } else {
                if (selectAll) selectAll.checked = false;
                if (selectionBanner && checkedCount === 0) {
                    selectionBanner.classList.remove('d-flex');
                    selectionBanner.classList.add('d-none');
                }
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                const isChecked = this.checked;
                selectItems.forEach(cb => cb.checked = isChecked);

                promoteAllFilteredInput.value = '0';
                if (allFilteredSelectedText) allFilteredSelectedText.classList.add('d-none');
                if (selectAllFilteredBtn) selectAllFilteredBtn.classList.remove('d-none');

                if (isChecked && selectItems.length > 0) {
                    if (selectionBanner) {
                        selectionBanner.classList.remove('d-none');
                        selectionBanner.classList.add('d-flex');
                    }
                } else {
                    if (selectionBanner) {
                        selectionBanner.classList.remove('d-flex');
                        selectionBanner.classList.add('d-none');
                    }
                }
                updateSelectionState();
            });
        }

        selectItems.forEach(cb => {
            cb.addEventListener('change', function () {
                promoteAllFilteredInput.value = '0';
                if (allFilteredSelectedText) allFilteredSelectedText.classList.add('d-none');
                if (selectAllFilteredBtn) selectAllFilteredBtn.classList.remove('d-none');
                updateSelectionState();
            });
        });

        if (selectAllFilteredBtn) {
            selectAllFilteredBtn.addEventListener('click', function () {
                promoteAllFilteredInput.value = '1';
                selectAllFilteredBtn.classList.add('d-none');
                if (allFilteredSelectedText) allFilteredSelectedText.classList.remove('d-none');
                updateSelectionState();
            });
        }

        if (clearSelectionBtn) {
            clearSelectionBtn.addEventListener('click', function () {
                promoteAllFilteredInput.value = '0';
                if (selectAll) selectAll.checked = false;
                selectItems.forEach(cb => cb.checked = false);
                if (allFilteredSelectedText) allFilteredSelectedText.classList.add('d-none');
                if (selectAllFilteredBtn) selectAllFilteredBtn.classList.remove('d-none');
                if (selectionBanner) {
                    selectionBanner.classList.remove('d-flex');
                    selectionBanner.classList.add('d-none');
                }
                updateSelectionState();
            });
        }

        // Promotion Modal & Submission Trigger
        const promoteBtn = document.getElementById('promote-btn');
        const modalPromotionDesc = document.getElementById('modal-promotion-desc');
        const confirmPromoteBtn = document.getElementById('confirm-promote-btn');
        const modalNotesInput = document.getElementById('modal-notes-input');
        const promotionNotes = document.getElementById('promotion-notes');

        if (promoteBtn) {
            promoteBtn.addEventListener('click', function () {
                const isAllFiltered = promoteAllFilteredInput.value === '1';
                const checkedItems = document.querySelectorAll('.select-item:checked');

                if (!isAllFiltered && checkedItems.length === 0) {
                    alert("Please select at least one student or choose 'Select all students matching this filter'.");
                    return;
                }

                if (isAllFiltered) {
                    modalPromotionDesc.innerHTML = `You are about to promote <strong>ALL ${totalMatchingStudents} students</strong> matching the current filter criteria to the next semester.`;
                } else {
                    modalPromotionDesc.innerHTML = `You are about to promote <strong>${checkedItems.length} selected student(s)</strong> on this page to the next semester.`;
                }

                $('#promoteModal').modal('show');
            });
        }

        if (confirmPromoteBtn) {
            confirmPromoteBtn.addEventListener('click', function () {
                if (promotionNotes && modalNotesInput) {
                    promotionNotes.value = modalNotesInput.value;
                }
                const form = document.getElementById('select-students-form');
                confirmPromoteBtn.disabled = true;
                confirmPromoteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Promoting...';
                form.submit();
            });
        }
    </script>
@endsection