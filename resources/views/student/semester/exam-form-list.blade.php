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
                @foreach ($examSessions as $examSession)
                    <tr>
                        <td>{{ $loop->index+1 }}</td>
                        <td>{{ $examSession->session_name }}</td>
                        <td>
                            @if($examSession->form_status==false)
                            <a href="{{ route('student.semester.exam-for-apply',$examSession->id) }}"> Apply </a>
                            @else
                                <button type="button" class="btn btn-primary viewSubjectBtn" data-toggle="modal" data-target="#exampleModal" data-id="{{ $examSession->id }}">
                                  View your locked subject
                               </button>
                               <a href="{{ route('student.semester.exam-for-apply',['session_id' => $examSession->id, 'edit' => true]) }}">Change Your Subject</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
      <div class="modal-content ">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Subject Select For this Exam</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div id="modal-data">
                <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                  </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('script_section')
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
crossorigin="anonymous"></script>
<script>

    $(document).on('click', '.viewSubjectBtn', function () {
        // Get the data-id from the button
        var examSessionId = $(this).data('id');
        // Fire an AJAX request to get the data
        $.ajax({
            url: '{{ url('student/semester/locked-subject-by-examsession') }}/'+examSessionId, // Your API endpoint
            method: 'GET',
            success: function (response) {
                // Populate the modal with the data
                $('#modal-data').html(response); // Assuming response contains HTML
            },
            error: function () {
                // Handle errors here
                $('#modal-data').html('An error occurred while loading the data.');
            }
        });

        // Show the modal after the AJAX call completes
        {{--  $('#exampleModal').modal('show');  --}}
    });

    

</script>
@endsection
