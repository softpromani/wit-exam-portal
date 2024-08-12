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
        ]);
        
        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $request->remember_me??false)) {
            return redirect()->route('admin.admin-dashboard')->with('success','Welcom to Admin Panel !!');
        } else {
            return redirect()->route('admin.admin-login')->with('error','Something Event Wrong !!');
        }
        
    }

    public function adminLogout(){
        Auth::logout();
        return redirect()->route('admin.admin-login')->with('sucess','Logout Successfully !!');
    }
}
