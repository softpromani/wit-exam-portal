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
use Carbon\Carbon;

class ExamController extends Controller
{
    //


    public function exam_form_list(){
       $examsession= ExamSession::get();
        return view('admin.exam.exam-form-list',compact('examsession'));
    }

    public function ExamSession($id){
        $examformdata=ExamForm::with('student','payment','exam_session')->where('session_id',$id)->get();        
        return response()->json(['status' => 1, 'examsession' => $examformdata]);
        // $examsession= ExamSession::get();
        // return view('admin.exam.exam-form-list',compact('examformdata','examsession'));
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
            $exist = ExamSchedule::where('exam_session_id',$request->exam_session )->where('subject_id', $request->subject)->exists();
            if($exist){
                $schedules = ExamSchedule::with(['subject','exam_session'])->get();
                return response()->json(['status' => 2, 'schedules' => $schedules]);
            }
            else
            {
                $data = ExamSchedule::create([
                    'exam_session_id'=>$request->exam_session,
                    'subject_id'=>$request->subject,
                    'date'=>$request->date,
                    'from_time'=>$request->from_time,
                    'to_time'=>$request->to_time,

                ]);

                $schedules = ExamSchedule::with(['subject','exam_session'])->get();
                if($data)
                {
                    return response()->json(['status' => 1, 'schedules' => $schedules]);
                }
                else{
                    return response()->json(['status' => 0, 'schedules' => $schedules]);
                }
            }

        }

        public function exam_form_show($exam_form_id){
            $examform = ExamForm::with(['subjects', 'student'])->find($exam_form_id);

        // Check if exam form exists
        if (!$examform) {
            // Handle case when no exam form is found
            return response()->json(['message' => 'No exam form found.'], 404);
        }

        $arrView['student'] = $examform->student;

        $arrView['subjects'] = $examform->subjects->map(function ($subject) use ($examform) {
            $schedule = ExamSchedule::where('exam_session_id', $examform->session_id)
                ->where('subject_id', $subject->id)
                ->first();
            $subject->date =$schedule?Carbon::parse(optional($schedule)->date)->format('d-M-Y'):'N/A';
            $startDate=Carbon::parse(optional($schedule)->from_time ? optional($schedule)->from_time:'00:00:00')->format('h:i a') ;
            $endDate=Carbon::parse(optional($schedule)->to_time ? optional($schedule)->to_time:'00:00:00')->format('h:i a');
            $subject->time=$startDate .' to '.$endDate;
            return $subject;
        });
        return view('student.semester.admitcard', $arrView);
        }
        public function fetchexam_schedule(){
            $schedules = ExamSchedule::with(['subject','exam_session'])->get();
            if( $schedules)
            {
              return response()->json(['status' => 1, 'schedules' => $schedules]);
            }
            else{
              return response()->json(['status' => 0, 'schedules' => $schedules]);
            }
        }

}
