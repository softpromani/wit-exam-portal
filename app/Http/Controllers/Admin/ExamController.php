<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ExamForm;
use App\Models\ExamFormSubject;
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
       $examsessions= ExamSession::get();
        return view('admin.exam.exam-form-list',compact('examsessions'));
    }

    public function ExamSession(Request $request){
        // dd($request->all());
        $examformdata=ExamForm::with('student','payment','exam_session')->where('session_id',$request->examsession)->get();
        // return response()->json(['status' => 1, 'examsession' => $examformdata]);
        $examsessions= ExamSession::get();
        return view('admin.exam.exam-form-list',compact('examformdata','examsessions'));
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
                ->where('subject_id', $subject->id)  // Order by date in ascending order
                ->first();
            $subject->date =$schedule?Carbon::parse(optional($schedule)->date)->format('d-M-Y'):'N/A';
            $startDate=Carbon::parse(optional($schedule)->from_time ? optional($schedule)->from_time:'00:00:00')->format('h:i a') ;
            $endDate=Carbon::parse(optional($schedule)->to_time ? optional($schedule)->to_time:'00:00:00')->format('h:i a');
            $subject->time=$startDate .' to '.$endDate;
            return $subject;
        })->sortBy('date');
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


        public function attendanceList(Request $req) {
            $examsession = ExamSession::get();
            $subject = Subject::get();
            $studentsData = [];  // Initialize an empty collection
            $selectedSubject=[];
            $selectedExamSession=(object)[];
            $examSchedule=(object)[];

            if ($req->filled('examsession') && $req->filled('subject')) {
                $session_id = $req->examsession;
                $subject_id = $req->subject;

                // Retrieve students based on the selected session and subject
                $studentsData = ExamForm::where('session_id', $session_id)
                    ->whereHas('examfrom_has_subjects', function($query) use ($subject_id) {
                        $query->where('subject_id', $subject_id);
                    })
                    ->with('student') // Make sure the student relationship is loaded
                    ->get()
                    ->pluck('student')
                    ->groupBy(function($student) {
                        return $student->branch->name; // Group by branch_name and branch_id
                    })->map(function ($students) {
                        // Order students by enrollment_no after grouping
                        return $students->sortBy('registration_no');
                    });
                $selectedExamSession=ExamSession::find($session_id);
                $selectedSubject=Subject::find($subject_id);
                $examSchedule=ExamSchedule::where('exam_session_id',$session_id)->where('subject_id',$subject_id)->first();
            }

            return view('admin.exam.attendance-list', compact('studentsData', 'examsession', 'subject','selectedExamSession','selectedSubject','examSchedule'));
        }
        public function attendanceData($students){
            return "gjg";
        }

        public function marksfeedList(Request $req){
            $marksfeed = ExamSession::get();
            $subject = Subject::get();
            $studentsData = [];
            $selectedSubject=[];
            $selectedExamSession=(object)[];
            $examSchedule=(object)[];

            if ($req->filled('examsession') && $req->filled('subject')) {
                $session_id = $req->examsession;
                $subject_id = $req->subject;

                // Retrieve students based on the selected session and subject
                $studentsData = ExamForm::where('session_id', $session_id)
                    ->whereHas('examfrom_has_subjects', function ($query) use ($subject_id) {
                        $query->where('subject_id', $subject_id);
                    })
                    ->with(['student', 'examfrom_has_subjects' => function ($query) use ($subject_id) {
                        // Make sure to filter the subjects by the subject_id
                        $query->where('subject_id', $subject_id);
                    }])
                    ->get()
                    ->map(function ($examForm) {
                        // Attach exam form information to the student
                        $examForm->student->exam_form_id = $examForm->id;

                        // Attach obtained marks and total marks from the examform_has_subjects relationship
                        $examFormSubject = $examForm->examfrom_has_subjects->first(); // Assuming there's only one related record per subject_id
                        $examForm->student->internal_mark = $examFormSubject ? $examFormSubject->internal_marks : null;
                        $examForm->student->external_mark = $examFormSubject ? $examFormSubject->external_marks : null;
                        $examForm->student->total_mark = $examFormSubject ? $examFormSubject->total_marks : null;

                        return $examForm->student;
                    })
                    ->groupBy(function ($student) {
                        return $student->branch->name;
                    })
                    ->map(function ($students) {
                        return $students->sortBy('registration_no');
                    });


                $selectedExamSession=ExamSession::find($session_id);
                $selectedSubject=Subject::find($subject_id);
                $examSchedule=ExamSchedule::where('exam_session_id',$session_id)->where('subject_id',$subject_id)->first();
            }
            // dd($studentsData);
            return view('admin.exam.marks-feed-list',compact('marksfeed' ,'studentsData',  'subject','selectedExamSession','selectedSubject','examSchedule'));
        }


        public function feedMarks(Request $request)
        {
            $isDataExist = ExamFormSubject::where('exam_form_id', $request->exam_form_id)->where('subject_id', $request->subject_id)->exists();

            if($isDataExist) {
                $obtainedMarks = $request->internal_mark + $request->external_mark;
                ExamFormSubject::where('exam_form_id', $request->exam_form_id)
                               ->where('subject_id', $request->subject_id)
                               ->update([
                                   'internal_marks' => $request->internal_mark,
                                   'external_marks' => $request->external_mark,
                                   'obtain_marks'   => $obtainedMarks,
                                   'total_marks'    => $request->total_mark,
                                 ]);

                $response = ['status' => 1, 'message' => 'Data feed successfully'];
            } else {
                $response = ['status' => 0, 'message' => 'Data not available'];
            }
            return response()->json($response);
        }
        // function for edit exam_schedule

        public function exam_schedule_edit($id){
            $examSchedule=ExamSchedule::findOrFail($id);
           $html='<div class="row">
           <input type="hidden" name="id" value="'.$id.'">
           <div class="col-sm-6"><label class="form-label">Exam Date</label></label><input type="date" class="form-control" name="date" value="'.$examSchedule->date.'"></div>
           <div class="col-sm-6"><label class="form-label">Exam From Time</label><input type="time" class="form-control" name="from_time" value="'.$examSchedule->from_time.'"></div>
           <div class="col-sm-6"><label class="form-label">Exam To Date</label><input type="time" class="form-control" name="to_time" value="'.$examSchedule->to_time.'"></div>
           <div class="col-sm-6"><label class="form-label">Exam L-T-P</label><input type="text" class="form-control" name="ltp" value="'.$examSchedule->ltp.'"></div>
           <div class="col-sm-6"><label class="form-label">Exam Credits</label><input type="number" class="form-control" name="credits" value="'.$examSchedule->credits.'"></div>
           ';
           return $html;
        }
        public function exam_schedule_update(Request $req){
            $examSchedule=ExamSchedule::findOrFail($req->id)->update([
                'date'=>$req->date,
                'from_time'=>$req->from_time,
                'to_time'=>$req->to_time,
                'ltp'=>$req->ltp,
                'credits'=>$req->credits
            ]);
            return redirect()->back();
        }
    }

