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
        $student = Student::findOrFail(Auth::guard('student')->id());

        if ($student->is_profile == 1) {
            return redirect()->route('student.dashboard')->with('error', 'Your profile is already locked. Please contact administrator if you need to make corrections.');
        }

        $hasPhoto = $student->profile_pic !== null;
        $hasSign = $student->sign !== null;

        $rules = [
            'student_name' => 'required',
            'gender' => 'required',
            'email' => 'required|email',
            'mobile_number' => 'required|max:13',
            'fname' => 'required',
            'mname' => 'required',
            'dob' => 'required|date',
            'adhar_number' => 'required|numeric',
            'parent_number' => 'required',
            'address' => 'required',
            'photo' => ($hasPhoto ? 'nullable' : 'required') . '|mimes:jpg,jpeg,png|max:512|dimensions:width=300,height=400',
            'signature' => ($hasSign ? 'nullable' : 'required') . '|mimes:jpg,jpeg,png|max:512|dimensions:width=200,height=100',
        ];

        $data = $request->validate($rules);

        $data['is_profile'] = '1';

        if ($request->hasFile('photo')) {
            if ($student->profile_pic) {
                $student->profile_pic->delete();
            }
            Media::uploadMedia($request->file('photo'), $student, 'photo');
        }

        if ($request->hasFile('signature')) {
            if ($student->sign) {
                $student->sign->delete();
            }
            Media::uploadMedia($request->file('signature'), $student, 'sign');
        }

        unset($data['photo'], $data['signature']);

        $updated = $student->update($data);

        if ($updated) {
            return redirect()->route('student.dashboard')->with('success', 'Your profile Update Successfully !!');
        } else {
            return redirect()->route('student.profile')->with('error', 'Something Went Wrong !!');
        }
    }


    public function logout(){
        Auth::guard('student')->logout();
        return redirect()->route('login');
    }

    public function file_update(Request $req){
        $rules = [
            'upload_id'=>'required|exists:media,id',
            'type'=>'required|in:photo,sign',
        ];

        if ($req->type == 'photo') {
            $rules['pic'] = 'required|mimes:jpg,jpeg,png|max:512|dimensions:width=300,height=400';
        } elseif ($req->type == 'sign') {
            $rules['pic'] = 'required|mimes:jpg,jpeg,png|max:512|dimensions:width=200,height=100';
        }

        $validated = $req->validate($rules);
        $media = Media::find($validated['upload_id']);
        $student = $media->mediable;

        if ($student instanceof Student && $student->is_profile == 1) {
            return redirect()->back()->with('error', 'Profile is locked. Please contact administrator to unlock your profile.');
        }

        if ($validated['type'] == 'photo') {
            $res = Media::uploadMedia($req->pic, $student, 'photo');
        } else if ($validated['type'] == 'sign') {
            $res = Media::uploadMedia($req->pic, $student, 'sign');
        }
        if (isset($res)) {
            $media->delete();
            return redirect()->back()->with('success', 'Your File Updated !!');
        }
        return redirect()->back()->with('error', 'Something went wrong !!');
    }

     public function getReciept(Request $request)
    {
     return view('admin.student.reciept');

    }
}
