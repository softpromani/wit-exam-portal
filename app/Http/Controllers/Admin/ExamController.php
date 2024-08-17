<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ExamForm;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Student;
class ExamController extends Controller
{
    //


    public function exam_form_list(){
        $examformdata=ExamForm::with('student')->get();
        // dd($examformdata);
        return view('admin.exam.exam-form-list',compact('examformdata'));
    }
}
