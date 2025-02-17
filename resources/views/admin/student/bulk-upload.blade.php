@extends('admin.includes.master')
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{route('admin.student.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row mb-4">
                    <div class="col-3">
                        <x-select-box name="registration_type" :options='["normal"=>"normal","lateral"=>"lateral"]' />
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="branch_id" class="form-lable">Branch</label>
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
                    </div>
                    <div class="col-3">
                        <x-select-box name="admission_session_id" :options="$admission_sessions" />
                    </div>

                    <div class="col-3">
                        <x-input-box type="file" name="excel" required/>
                    </div>
                    <div class="col-3">
                    <button type="submit" id="submitScheduleForm" class="btn btn-primary">Submit</button>

                    </div>
                </div>
            </form>
            <span class="text-danger"><a href="{{asset('wit/wit-student-import.xlsx')}}" download>Please! download Excell format from here</a></span>
        </div>
    </div>
</div>
@endsection