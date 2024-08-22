@extends('student.includes.master')
@section('style_area')
@endsection
@section('content')

    <div class="container">
        <div class="card">
            <div class="card-header d-none">
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-9">
                       <table class="table table-borderless">
                        <tr><th>Name :</th> <td colspan="4">{{$student->student_name}}</td></tr>
                        <tr>
                            <th>Registration No :</th> <td colspan="4">{{$student->registration_no}}</td>
                        </tr>
                        <tr><th>Registration Type :</th> <td>{{ucfirst($student->registration_type)}}</td>

                            <th>Roll No :</th> <td>{{$student->university_roll_no}}</td>
                        </tr>
                        <tr>
                            <th>Course :</th> <td>{{ucfirst(optional($student->course)->name)}}</td>
                            <th>Branch :</th> <td>{{ucfirst(optional($student->branch)->name)}}</td>
                        </tr>
                        <tr>
                            <th>Admission Session :</th> <td>{{ucfirst(optional($student->admission_session)->session_name)}}</td>
                            <th>Semester :</th> <td>{{ucfirst(optional($student->semester)->semester_name)}}</td>
                        </tr>
                       </table>
                    </div>
                    <div class="col-3">
                        <div class="col-6 mb-6 pt-3">
                            <img src="{{ asset('storage/' . optional(Auth::guard('student')->user()->profile_pic)->media) }}"
                                alt="" class="image-responsive" style="width: 150px; height: 200px;">
                        </div>
                        <div class="col-6 mb-6 mt-1">
                            <img src="{{ asset('storage/' . optional(Auth::guard('student')->user()->sign)->media) }}" alt=""
                                class="image-responsive" style="width: 150px; height: 50px;">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4 class="text-center">Session : 2023-24</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('student.semester.exam-apply') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <input type='hidden' name="exam_session_id" value='{{ $exam_session_id }}'/>
                            <label for="semester">Choose Your Subjects From here </label>
                            <select class="form-control" id="semester">
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary subject mt-4">Add Subject</button>
                        </div>

                        <div class="col-md-6 border-left border-danger ">
                            <h5 class="h5 text-dark">Subject You Choosen</h5>
                            <hr>
                            <table class="table table-borderless" id="choosen_subjects_table">
                                @forelse ($locked_subjects as $ls)
                                <tr>
                                    <td>{{ $ls->subject_code }} / {{$ls->title}}
                                        <input type="hidden" value="{{$ls->id}}" name="choosen_subjects[]" >
                                    </td>
                                    <td>
                                        <i class="fa-solid fa fa-trash text-danger delete-subject"></i>
                                    </td>

                                </tr>
                                @empty

                                @endforelse()
                            </table>
                        </div>
                        <div class="col-md-12 mt-2">
                            <p class="container">
                                <input type="checkbox" name="declaration" class="form-check-input fa-2x" value="true"
                                    required>

                                I have checked all the subjects and everything is correct. If there is any mistake in the
                                selection, I will be the responsible for it.
                            </p>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-primary float-right">Final Lock</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script_section')
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#semester').select2({
                //
                ajax: {
                    url: '{{ route('student.semester.fetchsubject') }}',
                    method: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            '_token': '{{ csrf_token() }}',
                            'param': params.term // Assuming params.term contains the search term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.subject_code + '/ ' + item.title

                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            });


            //
            $(document).on('click', '.subject', function() {
                var titleTxt = '';
                var titleId = null;
                titleId = $('#semester').val();
                titleTxt = $('#semester option:selected').text();
                var html = `
        <tr>
            <td>${titleTxt}
                <input type="hidden" value="${titleId}" name="choosen_subjects[]" >
            </td>
            <td>
                <i class="fa-solid fa fa-trash text-danger delete-subject"></i>
            </td>

        </tr>
        `;
                $('#choosen_subjects_table').append(html);
            });


        });
        $(document).on('click', '.delete-subject', function() {
            $(this).closest('tr').remove()
        });
    </script>
@endsection
