@extends('admin.includes.master')
@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Configure Exam Fees</h2>
            <a href="{{ route('admin.exam-session.index') }}" class="btn btn-outline-secondary rounded-pill shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Back to Sessions
            </a>
        </div>

        <div class="row">
            <!-- Configuration Form -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-primary py-3">
                        <h5 class="card-title text-white mb-0">Set New Amount Configuration</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.exam-session.store-amount') }}" method="POST">
                            @csrf
                            <input type="hidden" name="exam_session_id" value="{{ $examsession->id }}">

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-muted small text-uppercase">Course</label>
                                <select name="course_id"
                                    class="form-select form-select-lg rounded-3 border-light shadow-sm bg-light" required>
                                    <option value="" selected disabled>Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-muted small text-uppercase">Branch</label>
                                <select name="branch_id"
                                    class="form-select form-select-lg rounded-3 border-light shadow-sm bg-light" required>
                                    <option value="" selected disabled>Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="semesterAmountContainer">
                                <div class="semester-row mb-3 p-3 bg-light rounded-3 border">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-5">
                                            <label class="form-label small text-muted">Semester</label>
                                            <select name="semesters[]" class="form-select rounded-2 border-0 shadow-sm" required>
                                                <option value="" selected disabled>Select Semester</option>
                                                @foreach($semesters as $semester)
                                                    <option value="{{ $semester->id }}">{{ $semester->semester_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-5">
                                            <label class="form-label small text-muted">Amount (₹)</label>
                                            <input type="number" name="amounts[]"
                                                class="form-control rounded-2 border-0 shadow-sm" placeholder="e.g. 1500"
                                                required>
                                        </div>
                                        <div class="col-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm border-0 remove-row">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="addSemester"
                                class="btn btn-outline-primary btn-sm rounded-pill mb-4 w-100">
                                <i class="fas fa-plus me-2"></i>Add More Semester
                            </button>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow">
                                Save Configuration
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Configurations -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h5 class="card-title fw-bold text-dark mb-0">Active Fee Structures</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 border-0">Course & Branch</th>
                                        <th class="py-3 border-0">Semesters & Fees</th>
                                        <th class="py-3 border-0 text-end pe-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($existingConfigs as $config)
                                        <tr>
                                            <td class="px-4 py-4">
                                                <div class="fw-bold text-primary">{{ $config->course->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $config->branch->name ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                @if(is_array($config->semester_amounts))
                                                @foreach($config->semester_amounts as $semId => $amt)
                                                @php
                                                    $semName = $semesters->where('id', $semId)->first()->semester_name ?? 'Sem ' . $semId;
                                                @endphp
                                                    <span
                                                        class="badge bg-soft-primary text-primary border border-primary-subtle me-1 mb-1">
                                                        {{ $semName }}: ₹{{ $amt }}
                                                    </span>
                                                @endforeach
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <span
                                                    class="badge bg-success-subtle text-success rounded-pill px-3">Active</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted">
                                                <i class="fas fa-receipt fa-3x mb-3 d-block opacity-25"></i>
                                                No configurations found for this session.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-soft-primary {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }

        .form-select-lg {
            font-size: 1rem;
        }

        .semester-row {
            transition: all 0.2s;
        }

        .semester-row:hover {
            border-color: #0d6efd !important;
        }
    </style>

    <script>
        document.getElementById('addSemester').addEventListener('click', function () {
            const container = document.getElementById('semesterAmountContainer');
            const newRow = document.createElement('div');
            newRow.className = 'semester-row mb-3 p-3 bg-light rounded-3 border';
            newRow.innerHTML = `
            <div class="row g-3 align-items-end">
                <div class="col-5">
                    <label class="form-label small text-muted">Semester</label>
                    <select name="semesters[]" class="form-select rounded-2 border-0 shadow-sm" required>
                        <option value="" selected disabled>Select Semester</option>
                        @foreach($semesters as $semester)
                            <option value="{{ $semester->id }}">{{ $semester->semester_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-5">
                    <label class="form-label small text-muted">Amount (₹)</label>
                    <input type="number" name="amounts[]" class="form-control rounded-2 border-0 shadow-sm" placeholder="e.g. 1500" required>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-outline-danger btn-sm border-0 remove-row">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
            container.appendChild(newRow);
        });

        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-row')) {
                const row = e.target.closest('.semester-row');
                if (document.querySelectorAll('.semester-row').length > 1) {
                    row.remove();
                }
            }
        });
    </script>
@endsection