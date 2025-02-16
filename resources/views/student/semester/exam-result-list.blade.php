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
                @forelse ($examSession as $session)
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>{{ $session->session_name }}</td>
                        <td><a href="{{ route('student.semester.examresult-download',$session->id) }}"> Download Result </a></td>
                    </tr>
                @empty
                    {{--  <tr>
                        <td>1</td>
                        <td>Test Result</td>
                        <td><a href="{{ route('student.semester.examresult-download',1) }}"> Download Test Result </a></td>
                    </tr>  --}}
                @endforelse
                @if($pdfresult!=false)
                    <tr>
                        <td>1</td>
                        <td>current Semester Result</td>
                        <td>
                            <form action="{{route ('fetch-result') }}" method="POST">
                                @csrf
                                <input type="hidden" name="resultrollno" value="{{ $pdfresult }}">
                                <button type="submit" class="btn btn-primary">View Result</button>
                            </form>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

