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
                            <option value="{{ $subject->id }}">{{$subject->subject_code}} / {{ $subject->title }}</option>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function(){

        $.ajax({
            url: '{{ route("admin.fetch_exam_schedule") }}',
            type: 'GET',
            success: function(response) {
                console.log(response);
                var rows = '';
                // Assuming 'response' contains an array of exam schedules
                response.schedules.forEach(function(schedules, index) {
                    // Convert and format the date to 'd-M-YYYY'
                    var dateObj = new Date(schedules.date);
                    var formattedDate = dateObj.getDate() + '-' + 
                                        dateObj.toLocaleString('default', { month: 'short' }) + '-' + 
                                        dateObj.getFullYear();

                    rows += '<tr>'+
                                '<td>' + (index + 1) + '</td>'+
                                '<td>' + (schedules.exam_session ? schedules.exam_session.session_name : 'N/A') + '</td>'+
                                '<td>' + (schedules.subject ? schedules.subject.title : 'N/A') + '</td>'+
                                '<td>' + formattedDate + '</td>'+ // Use the formatted date here
                                '<td>' + schedules.from_time + '</td>'+
                                '<td>' + schedules.to_time + '</td>'+
                            '</tr>';
                });


                console.log(rows);
                $('#examScheduleTable').html(rows);
            },
            error: function(xhr, status, error) {
                console.error("An error occurred: " + error);
            }
        });


        $('#submitScheduleForm').click(function(e){
            e.preventDefault();
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
                  if(response.status==2)
                  {
                    Swal.fire({
                        title: 'Error!',
                        text: 'This schedule already exist!',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                      });
                  }
                  else if(response.status == 1 )
                  {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Schedule saved successfully!',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                      });
                  }
                    var rows = '';
                    // Assuming 'response' contains an array of exam schedules
                    response.schedules.forEach(function(schedules, index) {
                        rows += '<tr>'+
                                    '<td>' + (index + 1) + '</td>'+
                                    '<td>' + (schedules.exam_session ? schedules.exam_session.session_name : 'N/A') + '</td>'+
                                    '<td>' + (schedules.subject ? schedules.subject.title : 'N/A') + '</td>'+
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
