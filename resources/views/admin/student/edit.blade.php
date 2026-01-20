@extends('admin.includes.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Edit Student Details -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Student Details</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.student.update', $student->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label for="student_name">Student Name</label>
                                <input type="text" name="student_name" id="student_name" class="form-control"
                                    value="{{ old('student_name', $student->student_name) }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="registration_no">Registration No</label>
                                    <input type="number" name="registration_no" id="registration_no" class="form-control"
                                        value="{{ old('registration_no', $student->registration_no) }}" required>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="university_roll_no">University Roll No</label>
                                    <input type="text" name="university_roll_no" id="university_roll_no"
                                        class="form-control"
                                        value="{{ old('university_roll_no', $student->university_roll_no) }}">
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="branch_id">Branch</label>
                                <select name="branch_id" id="branch_id" class="form-control" required>
                                    <option value="">Select Branch</option>
                                    @foreach($courses as $course)
                                        <optgroup label="{{ $course->name }}">
                                            @foreach($course->branches as $branch)
                                                <option value="{{ $branch->id }}" {{ old('branch_id', $student->branch_id) == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group mb-3">
                                    <label for="semester_id">Current Semester</label>
                                    <select name="semester_id" id="semester_id" class="form-control" required>
                                        @foreach($semesters as $semester)
                                            <option value="{{ $semester->id }}" {{ old('semester_id', $student->semester_id) == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->semester_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group mb-3">
                                    <label for="admission_semester_id">Admission Semester</label>
                                    <select name="admission_semester_id" id="admission_semester_id" class="form-control"
                                        required>
                                        @foreach($semesters as $semester)
                                            <option value="{{ $semester->id }}" {{ old('admission_semester_id', $student->admission_semester_id) == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->semester_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="admission_session_id">Admission Session</label>
                                <select name="admission_session_id" id="admission_session_id" class="form-control" required>
                                    @foreach($admission_sessions as $session)
                                        <option value="{{ $session->id }}" {{ old('admission_session_id', $student->admission_session_id) == $session->id ? 'selected' : '' }}>
                                            {{ $session->session_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Update Details</button>
                                <a href="{{ route('admin.student.list') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Reset Password -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Reset Password</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.student.reset-password', $student->id) }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="password">New Password</label>
                                <input type="password" name="password" id="password" class="form-control" required
                                    minlength="6">
                            </div>
                            <div class="form-group mb-3">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" required minlength="6">
                            </div>
                            <button type="submit" class="btn btn-warning">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection