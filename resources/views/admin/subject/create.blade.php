@extends('admin.includes.master')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Add New Subject</h1>
            <p class="text-muted small mb-0">Create a new subject entry for exam schedules and student enrollment</p>
        </div>
        <a href="{{ route('admin.subject.index') }}" class="btn btn-secondary btn-sm shadow-sm font-weight-bold">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Back to Subjects
        </a>
    </div>

    <!-- Error Alerts -->
    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="font-weight-bold mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Please fix the following errors:</h6>
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
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-plus-circle mr-1"></i> Subject Details
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.subject.store') }}" method="POST">
                        @csrf

                        <!-- Academic Details Row -->
                        <div class="row">
                            <!-- Course -->
                            <div class="col-md-4 mb-3">
                                <label for="course_id" class="font-weight-bold text-gray-800">
                                    Course <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->name }} ({{ $course->full_name ?? $course->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Branch -->
                            <div class="col-md-4 mb-3">
                                <label for="branch_id" class="font-weight-bold text-gray-800">
                                    Branch <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                                    <option value="">Select Branch</option>
                                    @foreach($courses as $course)
                                        <optgroup label="{{ $course->name }}" data-course-id="{{ $course->id }}">
                                            @foreach($course->branches as $branch)
                                                <option value="{{ $branch->id }}" data-course="{{ $branch->course_id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }} - {{ $branch->full_name ?? $branch->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Semester -->
                            <div class="col-md-4 mb-3">
                                <label for="semester_id" class="font-weight-bold text-gray-800">
                                    Semester <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('semester_id') is-invalid @enderror" id="semester_id" name="semester_id" required>
                                    <option value="">Select Semester</option>
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem->id }}" {{ old('semester_id') == $sem->id ? 'selected' : '' }}>
                                            {{ $sem->semester_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('semester_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Subject Info Row -->
                        <div class="row">
                            <!-- Subject Code -->
                            <div class="col-md-4 mb-3">
                                <label for="subject_code" class="font-weight-bold text-gray-800">
                                    Subject Code <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control text-uppercase @error('subject_code') is-invalid @enderror"
                                       id="subject_code"
                                       name="subject_code"
                                       value="{{ old('subject_code') }}"
                                       placeholder="e.g. CS-1301"
                                       required>
                                @error('subject_code')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Subject Title -->
                            <div class="col-md-5 mb-3">
                                <label for="title" class="font-weight-bold text-gray-800">
                                    Subject Title / Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('title') is-invalid @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title') }}"
                                       placeholder="e.g. Object Oriented Programming"
                                       required>
                                @error('title')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Subject Type -->
                            <div class="col-md-3 mb-3">
                                <label for="type" class="font-weight-bold text-gray-800">
                                    Subject Type
                                </label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
                                    <option value="t" {{ old('type', 't') == 't' ? 'selected' : '' }}>Theory (t)</option>
                                    <option value="p" {{ old('type') == 'p' ? 'selected' : '' }}>Practical (p)</option>
                                    <option value="" {{ old('type') === '' ? 'selected' : '' }}>Not Specified</option>
                                </select>
                                @error('type')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.subject.index') }}" class="btn btn-secondary mr-2">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save mr-1"></i> Save Subject
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('footer-section')
<script>
    $(document).ready(function() {
        // Auto-select course when branch is selected, or filter branches when course is changed
        $('#course_id').on('change', function() {
            var selectedCourse = $(this).val();
            if (selectedCourse) {
                $('#branch_id optgroup').each(function() {
                    if ($(this).data('course-id') == selectedCourse) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            } else {
                $('#branch_id optgroup').show();
            }
        });

        $('#branch_id').on('change', function() {
            var selectedCourse = $(this).find('option:selected').data('course');
            if (selectedCourse && !$('#course_id').val()) {
                $('#course_id').val(selectedCourse);
            }
        });
    });
</script>
@endsection
