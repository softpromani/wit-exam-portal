@extends('admin.includes.master')

@section('style_area')
@endsection

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Student List</h1>
        <a href="{{ route('admin.student.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Student
        </a>
    </div>

    <!-- Overall Profile Completion Summary Cards -->
    <div class="row mb-4">
        <!-- Total Students -->
        <div class="col-xl-4 col-md-6 mb-3">
            <a href="{{ route('admin.student.list', ['fetch' => 1]) }}" style="text-decoration: none;">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Registered Students
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
        <div class="col-xl-4 col-md-6 mb-3">
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
        <div class="col-xl-4 col-md-6 mb-3">
            <a href="{{ route('admin.student.list', ['is_profile' => 0]) }}" style="text-decoration: none;">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Profile Incomplete / Pending
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
    </div>

    <!-- Filters Card -->
    <form action="{{route('admin.student.list')}}" method="post">
        @csrf
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter mr-1"></i> Filter Students
                </h6>
                @if(request()->has('is_profile') || request()->has('branch_id') || request()->has('semester_id') || request()->has('admission_session_id'))
                    <span class="badge badge-info">Filters Active</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
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
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label for="semester_id" class="font-weight-bold">Semester</label>
                        <select class="form-control" name="semester_id" id="semester_id">
                            <option value="">All Semesters</option>
                            @foreach($semesters as $sem)
                                <option value="{{$sem->id}}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                    {{$sem->semester_name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label for="admission_session_id" class="font-weight-bold">Admission Session</label>
                        <select class="form-control" name="admission_session_id" id="admission_session_id">
                            <option value="">All Sessions</option>
                            @foreach($admission_sessions as $ads)
                                <option value="{{$ads->id}}" {{ request('admission_session_id') == $ads->id ? 'selected' : '' }}>
                                    {{$ads->session_name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label for="is_profile" class="font-weight-bold">Profile Status</label>
                        <select class="form-control" name="is_profile" id="is_profile">
                            <option value="">All Statuses</option>
                            <option value="1" {{ (string)request('is_profile') === '1' ? 'selected' : '' }}>
                                Completed (Profile Submitted)
                            </option>
                            <option value="0" {{ (string)request('is_profile') === '0' ? 'selected' : '' }}>
                                Incomplete (Pending Submission)
                            </option>
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
                    <i class="fas fa-list mr-1"></i> Students ({{ $students->count() }})
                </h6>
                <div class="mt-2 mt-sm-0">
                    <span class="badge badge-success px-2 py-1 mr-1">
                        <i class="fas fa-check-circle"></i> {{ $students->where('is_profile', 1)->count() }} Completed
                    </span>
                    <span class="badge badge-danger px-2 py-1">
                        <i class="fas fa-times-circle"></i> {{ $students->where('is_profile', '!=', 1)->count() }} Incomplete
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
                    <form id="select-students-form">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="students-table">
                                <thead class="table-primary">
                                    <tr>
                                        <th style="width: 40px;"><input type="checkbox" id="select-all"></th>
                                        <th style="width: 50px;">Sr No</th>
                                        <th>Registration No</th>
                                        <th>University Roll No</th>
                                        <th>Name</th>
                                        <th>Branch</th>
                                        <th>Curr. Sem</th>
                                        <th>Adm. Sem</th>
                                        <th>Admission Session</th>
                                        <th>Profile Status</th>
                                        <th style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $st)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="select-item" name="selected_students[]" value="{{$st->id}}">
                                            </td>
                                            <td>{{$loop->index + 1}}</td>
                                            <td><strong>{{$st->registration_no}}</strong></td>
                                            <td>
                                                <input type="text" value="{{ $st->university_roll_no }}" data-id="{{ $st->id }}"
                                                    class="form-control form-control-sm roll-no-class" />
                                                <small class="text-danger error-message"></small>
                                            </td>
                                            <td>{{$st->student_name}}</td>
                                            <td>{{$st->branch?->name}}</td>
                                            <td>{{$st->semester?->semester_name}}</td>
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
                                    <tr>
                                        <th colspan="11">
                                            <button class="btn btn-danger btn-sm" type="button" id="promote-btn">
                                                <i class="fas fa-level-up-alt mr-1"></i> Promote Next Sem
                                            </button>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endisset

    <style>
        .loading {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" style="margin:auto; background:none; display:block;" width="20px" height="20px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="50" cy="50" fill="none" stroke="%2300f" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform></circle></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 20px 20px;
        }
    </style>
@endsection

@section('script_section')
    <script>
        document.querySelectorAll('.roll-no-class').forEach(function (input) {
            input.addEventListener('blur', function () {
                const value = this.value;
                const studentId = this.dataset.id;
                const errorEl = this.nextElementSibling;

                // Add loading state
                this.classList.add('loading');

                // Clear previous status
                this.classList.remove('border-success', 'border-danger');
                if (errorEl) errorEl.innerText = '';

                // Send AJAX request
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

        const selectAll = document.getElementById('select-all');
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                let checkboxes = document.querySelectorAll('.select-item');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }

        const promoteBtn = document.getElementById('promote-btn');
        if (promoteBtn) {
            promoteBtn.addEventListener('click', function () {
                let selectedStudents = document.querySelectorAll('.select-item:checked');

                if (selectedStudents.length === 0) {
                    alert("Please select at least one student.");
                    return;
                }

                let form = document.getElementById('select-students-form');
                form.action = "{{ url('admin/student-promote') }}";
                form.method = "POST";
                form.submit();
            });
        }
    </script>
@endsection