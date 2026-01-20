@extends('admin.includes.master')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Add New Student</h6>
                <a href="{{ route('admin.student.list') }}" class="btn btn-secondary btn-sm">Back to List</a>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.student.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- Personal Info -->
                        <div class="col-md-6 mb-3">
                            <label for="student_name">Student Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="student_name" name="student_name" value="{{ old('student_name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <!-- Registration / Roll No (Optional) -->
                        <div class="col-md-6 mb-3">
                            <label for="registration_no">Registration No <small class="text-muted">(Leave empty to auto-generate)</small></label>
                            <input type="text" class="form-control" id="registration_no" name="registration_no" value="{{ old('registration_no') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="university_roll_no">University Roll No <small class="text-muted">(Leave empty to auto-generate)</small></label>
                            <input type="text" class="form-control" id="university_roll_no" name="university_roll_no" value="{{ old('university_roll_no') }}">
                        </div>

                        <!-- Academic Info -->
                        <div class="col-md-6 mb-3">
                            <label for="registration_type">Registration Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="registration_type" name="registration_type" required>
                                <option value="normal" {{ old('registration_type') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="lateral" {{ old('registration_type') == 'lateral' ? 'selected' : '' }}>Lateral</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="branch_id">Branch <span class="text-danger">*</span></label>
                             <select class="form-control" name="branch_id" id="branch_id" required>
                                <option value="">Select Branch</option>
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

                        <div class="col-md-4 mb-3">
                            <label for="semester_id">Current Semester <span class="text-danger">*</span></label>
                            <select class="form-control" id="semester_id" name="semester_id" required>
                                <option value="">Select Semester</option>
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ old('semester_id') == $sem->id ? 'selected' : '' }}>{{ $sem->semester_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="admission_semester_id">Admission Semester <span class="text-danger">*</span></label>
                             <select class="form-control" id="admission_semester_id" name="admission_semester_id" required>
                                <option value="">Select Semester</option>
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ old('admission_semester_id') == $sem->id ? 'selected' : '' }}>{{ $sem->semester_name }}</option>
                                @endforeach
                            </select>
                        </div>
                         <div class="col-md-4 mb-3">
                            <label for="admission_session_id">Admission Session <span class="text-danger">*</span></label>
                            <select class="form-control" id="admission_session_id" name="admission_session_id" required>
                                <option value="">Select Session</option>
                                @foreach($admission_sessions as $session)
                                    <option value="{{ $session->id }}" {{ old('admission_session_id') == $session->id ? 'selected' : '' }}>{{ $session->session_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Create Student</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
