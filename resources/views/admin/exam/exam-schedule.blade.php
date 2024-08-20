@extends('admin.includes.master')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.exam_schedule_list') }}" id="examScheduleForm">
                @csrf
                <div class="row d-flex mb-3">
                    <div>
                        <label for="fordate" class="form-label">Exam Session</label>
                        <select class="form-control" aria-label="Default select example" name="exam_session">
                            <option selected value="">Exam Session</option>
                            @foreach ($examsessions as $schedule)
                            <option value="{{ $schedule->id }}">{{ $schedule->session_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3">
                        <label for="fordate" class="form-label">Subject</label>
                        <select class="form-control" aria-label="Default select example" name="subject">
                            <option selected value="">Subject</option>
                            @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-2">
                        <label for="fordate" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date1" name="date">
                    </div>
                    <div class="col-2">
                        <label for="fortime" class="form-label">From Time</label>
                        <input type="time" class="form-control" id="time1" name="from_time">
                    </div>

                    <div class="col-2">
                        <label for="totime" class="form-label">To Time</label>
                        <input type="time" class="form-control" id="totime" name="to_time">
                    </div>

                    <button type="submit" id="submitScheduleForm" class="btn btn-primary mt-3" >Submit</button>

                </div>
            </form>
        </div>
    </div>

    <div class="card mt-3" >
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th>Sr No</th>
                        <th>Exam Session</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>From Time</th>
                        <th>To Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="examScheduleTable">

                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
@section('script_section')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    {{--1 yahan se jquiry button ke liye h jo sab jagah hm syntax ye use kar sakte h   --}}
    $(document).ready(function(){
        $('#submitScheduleForm').click(function(e){
            e.preventDefault();
     {{--  1 end  ab real query jo form id ke adhaar par data ek $ver me store --}}
     // Collect form data using serialize() jo ki hm har form me use kar sakte h array me lata hai data
            var formData = $('#examScheduleForm').serialize();
            console.log(formData);
            // AJAX request
            $.ajax({
                url: '{{ route("admin.exam_schedule_store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log('data submitted');
                    console.log(response);
                    var rows = '';
                    // Assuming 'response' contains an array of exam schedules
                    response.schedules.forEach(function(schedules, index) {
                        rows += '<tr>'+
                                    '<td>' + (index + 1) + '</td>'+
                                    '<td>' + schedules.exam_session_id + '</td>'+
                                    '<td>' + schedules.subject_id + '</td>'+
                                    '<td>' + schedules.date + '</td>'+
                                    '<td>' + schedules.from_time + '</td>'+
                                    '<td>' + schedules.to_time + '</td>'+
                                '</tr>';
                    });
                    $('#examScheduleTable').html(rows);
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred: " + error);
                }
            });
        });
    });
</script>
@endsection
