<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ExamForm;
use App\Models\ExamSchedule;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Student;
use App\Models\Subject;

class ExamController extends Controller
{
    //


    public function exam_form_list(){
        $examformdata=ExamForm::with('student')->get();
        // dd($examformdata);
        return view('admin.exam.exam-form-list',compact('examformdata'));
    }

    public function exam_schedule(){
        $examschedule=ExamSchedule::get();
        // dd($request->all());
        $subjects=Subject::get();
        // dd($subjects);
        $examsessions=ExamSession::get();

        return view('admin.exam.exam-schedule',compact('examschedule','subjects', 'examsessions'));
    }
        public function exam_schedule_store(Request $request)
        {
            // dd($request->all());

          $data= ExamSchedule::create([
            'exam_session_id'=>$request->exam_session,
            'subject_id'=>$request->subject,
            'date'=>$request->date,
            'from_time'=>$request->from_time,
            'to_time'=>$request->to_time,

          ]);

          $schedules = ExamSchedule::get();
          if($data)
          {
            return response()->json(['status' => 1, 'schedules' => $schedules]);
          }
          else{
            return response()->json(['status' => 0, 'schedules' => $schedules]);
          }

        }

}
