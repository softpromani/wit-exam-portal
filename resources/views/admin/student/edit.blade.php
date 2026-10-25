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

                <!-- Profile Status & Unlock Option -->
                <div class="card mt-4 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Profile Lock Status</h3>
                        @if($student->is_profile == 1)
                            <span class="badge badge-success px-2 py-1"><i class="fas fa-lock mr-1"></i> Locked</span>
                        @else
                            <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-unlock-alt mr-1"></i> Unlocked</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($student->is_profile == 1)
                            <div class="alert alert-success py-2 px-3 small">
                                <i class="fas fa-check-circle mr-1"></i> <strong>Profile is currently Locked (Completed).</strong>
                                <p class="mb-0 text-muted mt-1">The student cannot edit their profile while it is locked. Click below to unlock their profile so the student can edit details.</p>
                            </div>
                            <form action="{{ route('admin.student.unlock-profile', $student->id) }}" method="POST" onsubmit="return confirm('Unlock profile for {{ addslashes($student->student_name) }} to allow editing?');">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-block font-weight-bold shadow-sm">
                                    <i class="fas fa-unlock-alt mr-1"></i> Unlock Profile (Allow Student to Edit)
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning py-2 px-3 small text-dark">
                                <i class="fas fa-info-circle mr-1"></i> <strong>Profile is currently Unlocked (Incomplete).</strong>
                                <p class="mb-0 text-muted mt-1">The student can currently edit and update their profile details on the portal. You can manually lock it if editing is complete.</p>
                            </div>
                            <form action="{{ route('admin.student.lock-profile', $student->id) }}" method="POST" onsubmit="return confirm('Lock profile for {{ addslashes($student->student_name) }}?');">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-block font-weight-bold shadow-sm">
                                    <i class="fas fa-lock mr-1"></i> Lock Profile
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection