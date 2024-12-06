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
                <form action="{{route('admin.marksfeed_list')}}" method="post" >
                    @csrf
                <div class="row">
                    <div class="col-md-4 ">
                        <select class="form-control mb-3 " name="examsession">
                            <option value="">-- Select Exam Session --</option>
                            @foreach ($marksfeed as  $examSession)
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

                            <div class="col-12 text-center">Marks Feed List</div>
                            <div class="col-12">
                                <b>Semester -</b> {{ $k}}
                            </div>
                            <div class="col-12">
                                <b>Branch -</b> {{ $k }}
                            </div>
                        </div>
                    </div>

                    <table  class="table table-bordered col-11 mt-3 ml-5" style="font-size:11px; color:black">
                        <thead>
                            <tr>
                                <th>Sr.No</th>
                                <th>Registration Number</th>
                                <th>Roll Number</th>
                                <th>Name</th>
                                <th>Internal Mark</th>
                                <th>External Mark</th>
                                <th>Total Mark (exam held marks like 10 cgpa)</th>
                                <th>Feed Marks</th>
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
                                    <form class="feedMarksForm{{ $dt->id }}">
                                        @csrf
                                        <input type="hidden" value="{{ $dt->exam_form_id }}" name="exam_form_id" />
                                        <input type="hidden" value="{{ $selectedSubject->id }}" name="subject_id" />
                                        <td><input class="form-control" type="text" name="internal_mark" value="{{ $dt->internal_mark ?? 0 }}" ></td>
                                        <td><input class="form-control" type="text" name="external_mark" value="{{ $dt->external_mark ?? 0 }}" ></td>
                                        <td><input class="form-control" type="text" name="total_mark" value="{{ $dt->total_mark ?? 0 }}" ></td>
                                        <td><button type="button" class="btn btn-primary feedbutton"  data-id="{{ $dt->id }}" >Feed</button></td>
                                    </form>

                                </tr>
                                @endforeach
                            @endisset


                        </tbody>

                    </table>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {

        $(document).on('click', '.feedbutton', function(e) {
            e.preventDefault();
            var formid=$(this).attr('data-id');

            // Serialize the form data
            var formData = $('.feedMarksForm'+formid).serialize();

            // AJAX request
            $.ajax({
              type: 'POST',
              url: '{{ route('admin.feedMarks') }}', // Replace with your server-side script
              data: formData,
              success: function(response) {
                console.log(response);
                if(response.status == 1){
                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'Ok'
                      })
                }
                else{
                    Swal.fire({
                        title: 'Error!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                      })
                }
              },
              error: function(xhr, status, error) {
                console.error(error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong!',
                    icon: 'info',
                    confirmButtonText: 'Ok'
                  })
              }
            });

        });

    });


    function printDiv(divId)
    {
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
