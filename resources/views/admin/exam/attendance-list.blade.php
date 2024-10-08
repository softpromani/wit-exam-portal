@extends('admin.includes.master')
@section('style_area')

@endsection
@section('content')
{{-- <style>
    @media print {
           /* Hide everything except the div with the given id */
           body * {
               visibility: hidden;
           }
           /* Show only the content of the specific div */
           .printable {
               visibility: visible !important;
           }
           /* Ensure the specific div takes up the entire page */
           /* .printable {
               position: absolute;
               top: 0;
               left: 0;
               width: 100%;
           } */
       }
</style> --}}
    <div class="container">
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.attendance_list')}}" method="post">
                    @csrf
                <div class="row">
                    <div class="col-md-4 ">
                        <select class="form-control mb-3 " name="examsession">
                            <option value="">-- Select Exam Session --</option>
                            @foreach ($examsession as  $examSession)
                            <option value="{{$examSession->id??''}}" @selected(isset($selectedExamSession->id) and $selectedExamSession->id==$examSession->id)>{{$examSession->session_name??''}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 ">
                        <select class="form-control mb-3 select2subjectcode" name="subject">
                            <option value="">-- Select Subject Session --</option>
                            @foreach ($subject as $sub)
                                <option value="{{ $sub->id?? '' }}" @selected(isset($selectedSubject->id) and $selectedSubject->id==$sub->id)>{{ $sub->subject_code ?? '' }} / {{ $sub->title ?? '' }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="col-md-2 ">
                        <input type="submit" class="form-control btn btn-primary"/>
                    </div>

                </div>
            </form>
            </div>
        </div>
    </div>
    @if(isset($studentsData) and !empty($studentsData))
    @foreach($studentsData as $k=>$students)
        <div class="container mt-3">
            <div class="card">
                <div class="card-header">Requested Data</div>
                <div class="card-body" id="printableArea{{$k}}">
                    <div class="row">
                        <div class="col-12">
                            <div id="header" style="height: 75px;border-bottom:1px solid grey;" >
                                <img src="{{ asset('wit/img/Dr.png') }}" alt="" style="width: 90%; height: 100%; object-fit: contain; margin-left:20px;">
                            </div>
                            <div class="col-12 text-center">Attendance List</div>
                            <div class="col-12">
                                @if($selectedExamSession and $selectedSubject and $examSchedule and $examSchedule->date and $examSchedule->from_time)
                                <b>Exam Session -</b> {{$selectedExamSession->session_name}} <br>
                                <b>Subject -</b> {{$selectedSubject->subject_code .' / '.$selectedSubject->title}} <br>
                                <b>Exam Date -</b> {{\Carbon\Carbon::parse($examSchedule->date)->format('d-M-Y')}}  &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;<b>Time -</b> {{\Carbon\Carbon::parse($examSchedule->from_time)->format('h:i a')}} to {{\Carbon\Carbon::parse($examSchedule->to_time)->format('h:i a')}} <br>
                                <b>Branch -</b> {{ $k }}
                                @else
                                    No Exam Schedule Found
                                @endif
                            </div>
                        </div>
                    </div>
                    <hr>
                    <table  class="table table-bordered" style="font-size:11px; color:black">
                        <thead>
                            <tr>
                                <th>Sr.No</th>
                                <th>Registration Number</th>
                                <th>Roll Number</th>
                                <th>Name</th>
                                <th>Answer Booklet Number</th>
                                <th>Student Signature</th>
                                <th>Invigilator Signature</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($students)
                                @foreach ($students as $dt)

                                <tr>
                                    <td>{{$loop->index+1}}</td>
                                    <td>{{$dt->registration_no}}</td>
                                    <td>{{$dt->university_roll_no}}</td>
                                    <td>{{$dt->student_name}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endforeach
                            @endisset

                        </tbody>
                    </table>
                        <button type='button' onclick="printDiv('printableArea{{$k}}')" id="attendancelist" class="btn btn-primary float-right">Print</button>
                </div>
            </div>
        </div>
    @endforeach
    @endif

@endsection
@section('script_section')
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // alert();
        $('.select2subjectcode').select2();
    });
    function printDiv(divId) {
         // Add class 'printable' to the specific div for printing
         var printContents = document.getElementById(divId).innerHTML;
        var originalContents = document.body.innerHTML;

        // Set the contents of the selected div to the body and print
        document.body.innerHTML = "<div class='printable'>" + printContents + "</div>";
        window.print();

        // Revert back to the original content after printing
        document.body.innerHTML = originalContents;
}
</script>
@endsection
