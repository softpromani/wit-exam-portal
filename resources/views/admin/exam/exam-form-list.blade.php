@extends('admin.includes.master')
@section('content')

      
    
<div class="container">

  <select class="form-control mb-3 col-md-4 examSession" name="examsession">
    <option value="">-- Select Exam Session --</option>
    @foreach ($examsession as  $examSession)
    <option value="{{$examSession->id??''}}">{{$examSession->session_name??''}}</option>
    @endforeach
  </select>

  
    <div class="card">   
        <div class="card-body">
            <h2>Applied Exam Form</h2>
            <table class="table table-bordered" id="student_datatable">
              <thead>
                  <tr>
                      <th>S.No</th>
                      <th>Roll No.</th>
                      <th>Registration No.</th>
                      <th>Session</th>
                      <th>Name</th>
                      <th>Mobile Number</th>
                      <th>CBS</th>
                      <th>Payment</th>
                      <th>Action</th>
                  </tr>
              </thead>
              <tbody id="examSessionTable"> 
              </tbody>
          </table>
        </div>
    </div>


</div>
<!-- Payment  modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModal" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Payment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form   action="{{ route('admin.fee-payment') }}" method="POST">
          @csrf
      <div class="modal-body">
              <div class="row">
                  <div class="col-6 mb-3">
                    {{--  form id regarding payment  --}}
                    <input type="hidden" name="form_id" id="Modalform_id">
                    <label for="payment_status" class="form-label">Payment Status</label>
                    <select class="form-control" name="payment_status">
                        <option value="">-- Select Payament Status --</option>
                        <option value="paid">Paid</option>
                        <option value="unpaid" >Unpaid</option>
                        <option value="partial">Partial Paid</option>
                    </select>
                  </div>
                  @error('payment_status')
                  <span class="text-danger">{{ $message }}</span>
                  @enderror
                <div class="col-6 mb-3">
                  <label for="payment_mode" class="form-label">Payment Mode</label>
                  <select class="form-control" name="payment_mode">
                      <option value="">-- Select Payament Mode --</option>
                      <option value="online">Online</option>
                      <option value="cash" >Cash</option>
                  </select>
              </div>
                @error('payment_mode')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-md-6 mb-3">
                  <label for="ammount">Amount</label>
                  <input type="text" class="form-control" id="validationDefault02" name="amount">
                </div>
                @error('ammount')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-md-6 mb-3">
                  <label for="transaction_id">Transcation Id</label>
                  <input type="text" class="form-control" id="validationDefault02" name="transaction_id">
                </div>
                @error('transaction_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
           </div>

      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Payment</button>
      </div>
  </form>

    </div>
  </div>
</div>
@endsection

@section('script_section')
<script src="https://code.jquery.com/jquery-3.7.1.js"
integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script>
    $(document).on('click','#paymentBtn',function(){
      var formid=  $(this).data('id');
      $('#Modalform_id').val(formid);
    });
    $(document).ready(function () {
        $('#student_datatable').DataTable({
            scrollX: true,  // Enables horizontal scrolling
            scrollY: 'auto',  // Sets vertical scroll height to 300px
            scroller: true,  // Enables the scroller feature
        });
    });

    $(document).on('change', '.examSession', function() {
    var sessionid = $(this).val(); // Get the selected session ID
      // alert(sessionid);
    if (sessionid!='') { // Check if a valid session ID is selected
        $.ajax({
            url: "{{url('admin/exam-session')}}/"+sessionid, // Use the route URL passed from Blade
            type: 'GET',
            success: function(response) {
              // console.log(response);
                if (response) {
                    console.log("Session ID:", response);
                    var rows = '';

                    response.examsession.forEach(function(examsession, index) {
                      
                      rows += '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + (examsession.student ? examsession.student.university_roll_no : 'N/A') + '</td>' +       
                        '<td>' + (examsession.student ? examsession.student.registration_no : 'N/A') + '</td>' +        
                        '<td>' + (examsession.exam_session ? examsession.exam_session.session_name : 'N/A') + '</td>' +        
                        '<td>' + (examsession.student ? examsession.student.student_name : 'N/A') + '</td>' +        
                        '<td>' + (examsession.student ? examsession.student.mobile_number : 'N/A') + '</td>' +        
                        '<td>' + 
                            '<span class="badge badge-primary">' + (examsession.course ? examsession.course.name : '') + '</span> ' +
                            '<span class="badge badge-success">' + (examsession.branch ? examsession.branch.name : '') + '</span> ' +
                            '<span class="badge badge-warning">' + (examsession.semester ? examsession.semester.semester_name : '') + '</span>' +
                        '</td>' +        
                    '</tr>';

                    });
                    console.log(rows);
                    $('#examSessionTable').html(rows);
                } else {
                    // Handle the case where the server returns success: false
                    alert("Failed to fetch session data.");
                }
            },
            error: function(xhr, status, error) {
                // Handle any errors during the AJAX request
                console.error("AJAX Error:", error);
                alert("An error occurred while processing your request.");
            }
        });
    } else {
        alert("Please select a valid session.");
    }
});


</script>
@endsection
