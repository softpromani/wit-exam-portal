@extends('student.includes.master')
@section('style_area')
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Sr No</th>
                    <th>Exam Session</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admitcardSession as $admitcardsSession)
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>{{ $admitcardsSession->session_name }}</td>
                        <td><a href="{{ route('student.semester.admitcard-download',$admitcardsSession->id) }}"> Download Admitcard </a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
