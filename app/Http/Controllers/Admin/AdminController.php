<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExamForm;
use App\Models\ExamSession;
use App\Models\Payment;

class AdminController extends Controller
{
    //
    public function adminDashboard(){
        $examsession= ExamSession::get();
        $examFormCount= ExamForm::count();
        $paystatus= Payment::where('payment_status','paid')->count();
        $totalFee= Payment::where('payment_status','paid')->sum('paid_amount');

       //dd($unpaidstatus);
        return view('admin.dashboard',compact('examFormCount','paystatus','totalFee','examsession'));
    }
}
