@extends('admin.includes.master')
@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <h2>Laravel Yajra Datatables Example</h2>
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
                          <tr>
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
                                <a href="#" class="ml-3" data-toggle="modal" data-target="#paymentModal"><i class="fa fa-credit-card text-success"></i></a>
                            </td>

                          </tr>
                      @endforeach  
                    @endif
                </tbody>
            </table>
        </div>
    </div>
   
</div>
@endsection

@section('script_section')
<script src="https://code.jquery.com/jquery-3.7.1.js"
integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        $('#student_datatable').DataTable({
            scrollX: true,  // Enables horizontal scrolling
            scrollY: 'auto',  // Sets vertical scroll height to 300px
            scroller: true,  // Enables the scroller feature
        });
    });
</script>
@endsection