@extends('student.includes.master')
@section('content')

<h1 class="h3 mb-2 text-gray-800">Student Profile</h1>
<div class="container card">

    <div class="card-body">
        
        <form action="{{($editstudent->is_profile==0)?route('student.store'):'#'}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-6 mb-3">
                    <label for="enrollment_number" class="form-label">University Roll No</label>
                    <input type="text" class="form-control" id="enrollment_number" name="enrollment_number"
                        value="{{ isset($editstudent) ?$editstudent->university_roll_no :'' }}" required  readonly/>
                </div> 
                @error('enrollment_number')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="reg_number" class="form-label">Registration Number</label>
                    <input type="text" class="form-control" id="reg_numbr" name="reg_number"
                        value="{{ isset($editstudent) ?$editstudent->registration_no :  '' }}" required readonly>
                </div>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="student_name"
                        placeholder="Enter your name"
                        value="{{ isset($editstudent) ?$editstudent->student_name :  old('student_name') }}">
                </div>
                @error('student_name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-control" name="gender">
                        <option value="">-- Select Gender --</option>
                        <option value="male" {{ isset($editstudent) && $editstudent->gender == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ isset($editstudent) && $editstudent->gender == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="transgender" {{ isset($editstudent) && $editstudent->gender == 'transgender' ? 'selected' : '' }}>Transgender</option>
                    </select>
                </div>                
                @error('gender')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email"
                        value="{{ isset($editstudent) ? $editstudent->email : old('email') }}">
                </div>
                @error('email')
                <span class="text-danger">{{ $message }}</span>
                @enderror

                <div class="col-6 mb-3">
                    <label for="mobile" class="form-label">Mobile No</label>
                    <input type="number" class="form-control" id="mobile_no" name="mobile_number"
                        placeholder="Enter your mobile_no"
                        value="{{ isset($editstudent) ? $editstudent->mobile_number :  old('mobile_number') }}">
                </div>
                @error('mobile_number')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="fname" class="form-label">Father name</label>
                    <input type="text" class="form-control" id="fname" name="fname"
                        placeholder="Enter your father name"
                        value="{{ isset($editstudent) ?$editstudent->fname :  old('name') }}">
                </div>
                @error('fname')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="mname" class="form-label">Mother Name</label>
                    <input type="text" class="form-control" id="mname" name="mname"
                        placeholder="Enter your name"
                        value="{{ isset($editstudent) ?$editstudent->mname :  old('mname') }}">
                </div>
                @error('mname')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-6 mb-3">
                    <label for="parent_number" class="form-label">Parent/Guardian Contact No.</label>
                    <input type="text" class="form-control" placeholder="Enter Parent/Guardian Number" id="parent_number" name="parent_number"
                        value="{{ isset($editstudent) ? $editstudent->parent_number  : '' }}">
                        @error('parent_number')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
               
                <div class="col-6 mb-3">
                    <label for="semester" class="form-label">Current Semester</label>
                    <input type="text" class="form-control" id="semester" name="semester"
                        value="{{ isset($editstudent) ? $editstudent->semester_id  : '' }}" readonly>
                </div>
                @error('parent_number')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="col-12 mb-3">
                    <label for="enrollment_number" class="form-label">Address</label>
                    <textarea class="form-control" name="address"> {{ isset($editstudent) ?$editstudent->address:'' }}</textarea>
                       
                </div>
                @if(!isset($editstudent->profile_pic))
                <div class="col-6 mb-3">
                    <label for="photo" class="form-label">Upload Picture <span class="text-danger">(must be 300x400 px and less than 512 Kb)</span></label>
                    <input type="file" class="form-control" id="photo" name="photo"/>
                    <img id="profilePreview" src="" width="60" height="60" class="mt-2" style="display: none;" />
                    @error('photo')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                @else
                <div class="col-6 mb-3">
                    <b>Your Profile Pic</b> <br>
                    <img src="{{asset('storage/'.$editstudent->profile_pic->media)}}" alt="" class="image-responsive" style="width: 150px; height: 200px;">
                </div>
                @endif
                
                @if(!isset($editstudent->sign))
                <div class="col-6 mb-3">
                    <label for="signature" class="form-label">Signature  <span class="text-danger">(must be 200x100 px and less than 512 Kb)</span></label>
                    <input type="file" class="form-control" id="signature" name="signature"/>
                    <img id="signaturePreview" src="" width="60" height="60" class="mt-2" style="display: none;" />
                    @error('signature')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                @else
                    <div class="col-6 mb-6 pt-5">
                        <b>Your Signature</b><br>
                        <img src="{{asset('storage/'.$editstudent->sign->media)}}" alt="" class="image-responsive" style="width: 300px; height: 100px;">
                    </div>
                @endif
             
                <div class="col-12 mt-3">
                    {{-- {{isset($editstudent) && $editstudent->is_profile == 1?'disabled':''}} --}}
                    <button type="submit" class="btn btn-primary" {{($editstudent->is_profile==1)?'disabled':''}} >Update</button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection

 @section('script_section')
 <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script>
        // $(document).ready(function () {
        //     // Event listener for photo input change
        //     $('#photo').on('change', function (event) {
        //         alert();
        //         var file = event.target.files[0];
        //         if (file) {
        //             var reader = new FileReader();
        //             reader.onload = function (e) {
        //                 $('#profilePreview').attr('src', e.target.result).show();
        //             };
        //             reader.readAsDataURL(file);
        //         } else {
        //             // Hide the preview if no file is selected
        //             $('#profilePreview').hide();
        //         }
        //     });
        // });


    </script>
 @endsection              
               