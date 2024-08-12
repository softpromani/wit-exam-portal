<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExamForm;
class AdminController extends Controller
{
    //
    public function adminDashboard(){
        $arrView['examFormCount']= ExamForm::where('student_id', auth()->guard('student')->id())->count();
        return view('admin.dashboard',$arrView);
    }
}
