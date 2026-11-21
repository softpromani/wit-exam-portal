@extends('admin.includes.master')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                <i class="fas fa-file-csv text-success mr-2"></i> Import Subjects via CSV / Excel
            </h1>
            <p class="text-muted small mb-0">
                Bulk upload subjects from a CSV or Excel spreadsheet instead of manual entry or raw database imports
            </p>
        </div>
        <div>
            <a href="{{ route('admin.subject.sample-csv') }}" class="btn btn-outline-success btn-sm shadow-sm font-weight-bold mr-2">
                <i class="fas fa-download mr-1"></i> Download Sample CSV
            </a>
            <a href="{{ route('admin.subject.index') }}" class="btn btn-secondary btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-arrow-left fa-sm mr-1"></i> Back to Subjects
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
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

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="font-weight-bold mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Please check the following errors:</h6>
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Upload Form -->
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cloud-upload-alt mr-1"></i> Upload File
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.subject.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- File Input -->
                        <div class="form-group mb-4">
                            <label for="file" class="font-weight-bold text-gray-800">
                                Select CSV or Excel File (.csv, .xlsx, .xls) <span class="text-danger">*</span>
                            </label>
                            <input type="file"
                                   class="form-control-file border rounded p-2 bg-light @error('file') is-invalid @enderror"
                                   name="file"
                                   id="file"
                                   accept=".csv,.xlsx,.xls,.txt"
                                   required>
                            <small class="form-text text-muted">
                                Supported formats: Standard CSV or Microsoft Excel (.xlsx, .xls). Maximum size: 50MB.
                            </small>
                            @error('file')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Duplicate Handling -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-gray-800 d-block">Duplicate Record Strategy</label>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="duplicate_skip" name="duplicate_action" value="skip" class="custom-control-input" checked>
                                <label class="custom-control-label" for="duplicate_skip">
                                    <strong>Skip duplicates</strong> (Keep existing database records)
                                </label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="duplicate_update" name="duplicate_action" value="update" class="custom-control-input">
                                <label class="custom-control-label" for="duplicate_update">
                                    <strong>Update existing</strong> (Overwrite title & type with CSV data)
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Optional Default Academic Context -->
                        <div class="mb-3">
                            <h6 class="font-weight-bold text-gray-800 mb-1">
                                Default Academic Mapping <span class="badge badge-light border text-muted">Optional</span>
                            </h6>
                            <p class="small text-muted mb-3">
                                If your CSV already has <code>course</code>, <code>branch</code>, and <code>semester</code> columns, they will be read row by row. If any row is missing these or if you want to import a list for a specific class, select defaults below:
                            </p>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="default_course_id" class="small font-weight-bold">Default Course</label>
                                <select class="form-control form-control-sm" name="default_course_id" id="default_course_id">
                                    <option value="">-- Read from CSV --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="default_branch_id" class="small font-weight-bold">Default Branch</label>
                                <select class="form-control form-control-sm" name="default_branch_id" id="default_branch_id">
                                    <option value="">-- Read from CSV --</option>
                                    @foreach($courses as $course)
                                        <optgroup label="{{ $course->name }}" data-course-id="{{ $course->id }}">
                                            @foreach($course->branches as $branch)
                                                <option value="{{ $branch->id }}" data-course="{{ $branch->course_id }}">
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="default_semester_id" class="small font-weight-bold">Default Semester</label>
                                <select class="form-control form-control-sm" name="default_semester_id" id="default_semester_id">
                                    <option value="">-- Read from CSV --</option>
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem->id }}">{{ $sem->semester_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4 text-right">
                            <a href="{{ route('admin.subject.index') }}" class="btn btn-secondary mr-2">Cancel</a>
                            <button type="submit" class="btn btn-success px-4 font-weight-bold">
                                <i class="fas fa-upload mr-1"></i> Upload and Import
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Guidelines / CSV Template Help -->
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-dark">
                        <i class="fas fa-info-circle text-info mr-1"></i> CSV Format & Column Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-gray-700">
                        The first row of your CSV file must contain column headers. Header names are case-insensitive.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm small mb-3">
                            <thead class="thead-light">
                                <tr>
                                    <th>Column</th>
                                    <th>Required</th>
                                    <th>Example / Acceptable Values</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>subject_code</code></td>
                                    <td><span class="badge badge-danger">Required</span></td>
                                    <td><code>CS-1301</code>, <code>HS-1102</code></td>
                                </tr>
                                <tr>
                                    <td><code>title</code></td>
                                    <td><span class="badge badge-danger">Required</span></td>
                                    <td><code>Object Oriented Programming</code></td>
                                </tr>
                                <tr>
                                    <td><code>course</code></td>
                                    <td><span class="badge badge-warning text-dark">Flexible</span></td>
                                    <td>Course name (<code>B.tech</code>, <code>BCA</code>, <code>MCA</code>) or Course ID (<code>1</code>)</td>
                                </tr>
                                <tr>
                                    <td><code>branch</code></td>
                                    <td><span class="badge badge-warning text-dark">Flexible</span></td>
                                    <td>Branch name (<code>CSE</code>, <code>IT</code>, <code>BCA</code>) or Branch ID (<code>1</code>)</td>
                                </tr>
                                <tr>
                                    <td><code>semester</code></td>
                                    <td><span class="badge badge-warning text-dark">Flexible</span></td>
                                    <td><code>sem-1</code>, <code>sem-3</code>, or number (<code>1</code> to <code>8</code>)</td>
                                </tr>
                                <tr>
                                    <td><code>type</code></td>
                                    <td><span class="badge badge-secondary">Optional</span></td>
                                    <td><code>Theory</code> (or <code>t</code>), <code>Practical</code> (or <code>p</code>)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 bg-light rounded border">
                        <h6 class="font-weight-bold small text-dark mb-2">Example CSV Contents:</h6>
                        <pre class="mb-0 text-dark small" style="font-family: monospace; font-size: 0.8rem; background: #fff; padding: 8px; border-radius: 4px; border: 1px solid #e3e6f0;">subject_code,title,course,branch,semester,type
CS-1301,Object Oriented Programming,B.tech,CSE,sem-3,Theory
CS-1302,OOP Lab,B.tech,CSE,sem-3,Practical
MA-1101,Mathematics-1,B.tech,IT,sem-1,Theory
BCA-101,Computer Fundamentals,BCA,BCA,sem-1,Theory</pre>
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.subject.sample-csv') }}" class="btn btn-outline-primary btn-sm btn-block">
                            <i class="fas fa-file-download mr-1"></i> Download Pre-Filled CSV Template
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('footer-section')
<script>
    $(document).ready(function() {
        $('#default_course_id').on('change', function() {
            var selectedCourse = $(this).val();
            if (selectedCourse) {
                $('#default_branch_id optgroup').each(function() {
                    if ($(this).data('course-id') == selectedCourse) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            } else {
                $('#default_branch_id optgroup').show();
            }
        });

        $('#default_branch_id').on('change', function() {
            var selectedCourse = $(this).find('option:selected').data('course');
            if (selectedCourse && !$('#default_course_id').val()) {
                $('#default_course_id').val(selectedCourse);
            }
        });
    });
</script>
@endsection
