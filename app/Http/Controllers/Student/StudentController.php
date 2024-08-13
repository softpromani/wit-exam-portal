<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamForm;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
class StudentController extends Controller
{
    //
    public function dashboard(){
        $arrView['examFormCount']= ExamForm::where('student_id', auth()->guard('student')->id())->count();
        return view('student.dashboard',$arrView);
    }

    public function studentProfile(){
        $editstudent=Student::find(Auth::guard('student')->id());
        return view('student.profile',compact('editstudent'));
    }

    public function store(Request $request){
        $data = $request->validate([
            'student_name' => 'required',
            'gender' => 'required',
            'email' => 'required|email',
            'mobile_number' => 'required|max:13',
            'fname' => 'required',
            'mname' => 'required',
            'parent_number' => 'required',
            'address' => 'required',
            'photo'=>'nullable|mimes:jpg,jpeg,png|max:512|dimensions:width=300,height=400',
            'signature'=>'nullable|mimes:jpg,jpeg,png|max:512|dimensions:width=200,height=100',
        ]);
            // dd($data);
        $student=Student::findOrFail(Auth::guard('student')->id());
        $data['is_profile']='1';
             isset($request->photo,$student)?Media::uploadMedia($request->photo,$student,'photo'):'';
              isset($request->signature)?Media::uploadMedia($request->signature,$student,'sign'):'';
              $student->update($data);
        if($student){
            return redirect()->route('student.dashboard')->with('success','Your profile Update Successfully !!');
        }else{
            return redirect()->route('student.profile')->with('success','Something Event Wrong !!');
        }
    }


    public function logout(){
        Auth::guard('student')->logout();
        return redirect()->route('login');
    }
}
