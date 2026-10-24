<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class AdminAuthController extends Controller
{
    //

    
    public function login(){
        return view('admin.auth.login');
    }

    public function adminLogin(Request $request){
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required|captcha',
        ], [
            'captcha.required' => 'Please enter the captcha code.',
            'captcha.captcha' => 'Invalid captcha code.',
        ]);
        
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $request->remember_me??false)) {
            session()->forget('captcha_code');
            return redirect()->route('admin.admin-dashboard')->with('success','Welcome to Admin Panel !!');
        } else {
            return redirect()->route('admin.admin-login')
                ->withInput($request->only('email', 'remember_me'))
                ->with('error','Invalid Credentials !!');
        }
        
    }

    public function adminLogout(){
        Auth::logout();
        return redirect()->route('admin.admin-login')->with('sucess','Logout Successfully !!');
    }
}
