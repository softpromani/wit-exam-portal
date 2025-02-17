@extends('admin.includes.master')

@section('style_area')
@endsection

@section('content')
<form action="{{route('admin.student.list')}}" method="post">
    @csrf
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-4">
                <label for="branch_id">Branch</label>
                <select class="form-control" name="branch_id" id="branch_id">
                    @foreach($courses as $course)
                        <optgroup label="{{ $course->name }}">
                            @foreach($course->branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div class="col-4">
                <label for="semester_id">Semester</label>
                <select class="form-control" name="semester_id" id="semester_id">
                    @foreach($semesters as $sem)
                    <option value="{{$sem->id}}">{{$sem->semester_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4">
                <label for="admission_session_id">Admission Session</label>
                <select class="form-control" name="admission_session_id" id="admission_session_id">
                    @foreach($admission_sessions as $ads)
                    <option value="{{$ads->id}}">{{$ads->session_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-4 mt-3">
                <button class="btn btn-primary" type="submit">Fetch Student</button>
            </div>
        </div>
    </div>
</div>
</form>
@isset($students)
    <div class="card mt-3">
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-primary"><tr><th>Sr No</th><th>Registration No</th><th>University Roll No</th><th>Name</th><th>Branch</th><th>Admission Session</th></tr></thead>
            
            <tbody>
                @foreach($students as $st)
                <tr>
                    <td>{{$loop->index+1}}</td>
                    <td>{{$st->registration_no}}</td>
                    <td>{{$st->university_roll_no}}</td>
                    <td>{{$st->student_name}}</td>
                    <td>{{$st->branch?->name}}</td>
                    <td>{{$st->admission_session?->session_name}}</td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
    </div>
@endisset

@endsection

