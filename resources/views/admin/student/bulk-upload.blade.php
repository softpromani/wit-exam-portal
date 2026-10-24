@extends('admin.includes.master')
@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-file-excel text-success mr-2"></i> Bulk Student Import
            </h1>
            <p class="text-muted small mb-0">
                Upload student rosters in Excel (.xlsx, .xls) or CSV format. Optimized for high-volume uploads (lakhs of records) with fast batch processing.
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

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <strong><i class="fas fa-exclamation-circle mr-1"></i> Please check the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-upload mr-1"></i> Upload Roster File
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.student.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label for="registration_type" class="font-weight-bold">Registration Type <span class="text-danger">*</span></label>
                        <select class="form-control" name="registration_type" id="registration_type" required>
                            <option value="normal" {{ old('registration_type') === 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="lateral" {{ old('registration_type') === 'lateral' ? 'selected' : '' }}>Lateral</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <label for="branch_id" class="font-weight-bold">Branch <span class="text-danger">*</span></label>
                        <select class="form-control" name="branch_id" id="branch_id" required>
                            <option value="">-- Select Branch --</option>
                            @foreach($courses as $course)
                                <optgroup label="{{ $course->name }}">
                                    @foreach($course->branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <label for="admission_session_id" class="font-weight-bold">Admission Session <span class="text-danger">*</span></label>
                        <select class="form-control" name="admission_session_id" id="admission_session_id" required>
                            <option value="">-- Select Session --</option>
                            @foreach($admission_sessions as $id => $name)
                                <option value="{{ $id }}" {{ old('admission_session_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6 mb-3">
                        <label for="excel" class="font-weight-bold">File (.xlsx, .csv, .xls) <span class="text-danger">*</span></label>
                        <input type="file" name="excel" id="excel" class="form-control-file border rounded p-1" accept=".xlsx,.csv,.xls,.txt" required>
                    </div>

                    <div class="col-12 mt-3 d-flex align-items-center justify-content-between flex-wrap">
                        <button type="submit" id="submitScheduleForm" class="btn btn-primary font-weight-bold shadow-sm">
                            <i class="fas fa-file-import mr-1"></i> Start Import Process
                        </button>
                        <div class="mt-2 mt-sm-0">
                            <a href="{{ asset('wit/wit-student-import.xlsx') }}" download class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download mr-1"></i> Download Sample Excel Template
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <hr class="my-4">

            <div class="alert alert-info py-3 px-4 mb-0">
                <h6 class="font-weight-bold mb-2">
                    <i class="fas fa-lightbulb text-warning mr-1"></i> Import Instructions & Best Practices:
                </h6>
                <ul class="mb-0 small">
                    <li>The Excel/CSV sheet must contain columns: <code>name</code>, <code>password</code>, and <code>semester</code>.</li>
                    <li>For very large datasets (lakhs of students), <strong>CSV format</strong> is recommended for maximum throughput.</li>
                    <li>University Roll Numbers and Registration Numbers are generated automatically with sequential uniqueness.</li>
                    <li>Password hashing and database inserts are executed in high-performance chunks of 1,000 students to prevent timeouts.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection