@extends('admin.includes.master')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h2>Applied Exam Form</h2>
            <table class="table table-bordered" id="student_datatable">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Roll No.</th>
                        <th>Registeration No.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>DOB</th>
                        <th>Adhar Number</th>
                        <th>Mobile Number</th>
                        <th>Course</th>
                        <th>Branch</th>
                        <th>Semester</th>
                        <th>Admission Session</th>
                        <th>Father Name</th>
                        <th>Mother Name</th>
                        <th>Parent Number</th>
                        <th>Address</th>

                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($examformdata!='')
                      @foreach ($examformdata as  $formdata)
                          {{--  <tr>
                            <td>{{$loop->index+1}}</td>
                            <td>{{$formdata->university_roll_no??''}}</td>
                            <td>{{$formdata->registration_no??''}}</td>
                            <td>{{$formdata->student_name??''}}</td>
                            <td>{{$formdata->email??''}}</td>
                            <td>{{$formdata->gender??''}}</td>
                            <td>{{$formdata->dob??''}}</td>
                            <td>{{$formdata->adhar_number??''}}</td>
                            <td>{{$formdata->mobile_number??''}}</td>
                            <td>{{$formdata->course->name??''}}</td>
                            <td>{{$formdata->branch->name??''}}</td>
                            <td>{{$formdata->semester->semester_name??''}}</td>
                            <td>{{$formdata->admission_session->session_name??''}}</td>
                            <td>{{$formdata->fname??''}}</td>
                            <td>{{$formdata->mname??''}}</td>
                            <td>{{$formdata->parent_number??''}}</td>
                            <td>{{$formdata->address??''}}</td>
                            <td>
                                <a href="#"><i class="fa fa-eye text-primary"></i></a>
                               @if($formdata->student->is_profile == 1)
                                <a href="#" class="ml-3" data-toggle="modal" data-target="#paymentModal" data-id="{{ $formdata->id }}" id="paymentBtn"><i class="fa fa-credit-card text-success"></i></a> 
                               @endif
                                   
                            </td>

                          </tr>  --}}
                      @endforeach
                    @endif
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
</script>
@endsection
