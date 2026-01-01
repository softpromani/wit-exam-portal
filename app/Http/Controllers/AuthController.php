<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    //
    public function index()
    {
        return view('auth.login');
    }

    public function resultview()
    {
        return view('result.view');
    }

    public function fetch_result(Request $request)
    {

        $data = $request->validate([
            'resultrollno' => 'required',
        ]);
        if (Storage::exists('result/' . $request->resultrollno . '.pdf')) {
            // return response()->file(storage_path(path: 'app/result/' . $request->resultrollno.'.pdf'));
            $filePath = storage_path('app/result/' . $request->resultrollno . '.pdf');

            // Return response to open file in a new tab
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $request->resultrollno . '.pdf"',
            ]);
        }
        return redirect()->back()->with('error', 'Result Not Available');

    }

    public function login(Request $request)
    {

        $data = $request->validate([
            'university_roll_no' => 'required',
            'password' => 'required',
            'remember_me' => 'sometimes|boolean'
        ]);
        if (
            Auth::guard('student')->attempt([
                'registration_no' => $data['university_roll_no'],
                'password' => $data['password']
            ], $request->remember_me ?? false)
        ) {
            if (Auth::guard('student')->user()->is_profile == 1) {
                return redirect()->route('student.dashboard');
            } else {
                return redirect()->route('student.profile')->with('success', 'Welcome to student panel !!');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid Credentials');
        }

    }

    // change password

    public function changePassword(Request $request, $id)
    {
        $data = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);

        $studentData = Student::find($id);

        if (Hash::check($data['old_password'], $studentData->password)) {
            $studentData->password = Hash::make($data['new_password']);
            $studentData->save();

            return redirect()->route('login')->with('success', 'Password change successfully.');
        } else {
            return redirect()->back()->with('error', 'Old password is incorrect.');
        }
    }

}
