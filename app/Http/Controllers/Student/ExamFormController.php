<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamFormApplicationReq;
use App\Models\ExamForm;
use App\Models\ExamSession;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Subject;
use DB;

class ExamFormController extends Controller
{
    function exam_form()
    {
        $student = auth()->guard('student')->user(); // Get the authenticated student once

        $examSessions = ExamSession::where('status', 'process')->get()->map(function ($examSession) use ($student) {
            // Call the method to get the form status for the exam session
            $formStatus = $student->checkThisSemFormStatus($examSession->id);

            // Add the form status as a new attribute to the exam session
            $examSession->form_status = $formStatus;

            // Return the modified exam session with the new key
            return $examSession;
        });
        return view('student.semester.exam-form-list', ['examSessions' => $examSessions]);
    }
    function apply_for_exam($exam_session_id)
    {
        if (auth()->guard('student')->user()->checkThisSemFormStatus($exam_session_id)) {
            return redirect()->back()->with(['warning' => 'Your Exam Form for  this Session already submitted']);
        }
        $student = auth()->guard('student')->user();
        return view('student.semester.exam-form', compact('student', 'exam_session_id'));
    }
    public function subject_fetch(Request $request)
    {
        $subjects = Subject::select('id', 'subject_code', 'title')->where('subject_code', 'LIKE', '%' . $request->param . '%')->orWhere('title', 'LIKE', '%' . $request->param . '%')
            ->limit(10)
            ->get();
        return response()->json($subjects, 200);
    }
    public function apply_exam_form(ExamFormApplicationReq $req)
    {
        $data = $req->validated();
        $data['session_id'] = $req->exam_session_id;
        $data['semester_id'] = auth()->guard('student')->user()->semester_id;
        $data['student_id'] = auth()->guard('student')->id();
        try {
            // Start a database transaction
            DB::transaction(function () use ($data) {
                // Create or get the ExamForm
                $examForm = ExamForm::firstOrCreate(
                    [
                        'session_id' => $data['session_id'],
                        'semester_id' => $data['semester_id'],
                        'student_id' => $data['student_id']
                    ],
                    $data
                );

                // Prepare exam_form_subjects data
                $examFormSubjects = array_map(function ($subjectId) use ($examForm) {
                    return [
                        'exam_form_id' => $examForm->id,
                        'subject_id' => $subjectId,
                        'total_marks' => 0, // Default value or use request data
                        'obtain_marks' => 0, // Default value or use request data
                        'grade' => null, // Default value or use request data
                    ];
                }, $data['choosen_subjects']);

                // Clear existing subjects for this exam form
                DB::table('exam_form_subjects')->where('exam_form_id', $examForm->id)->delete();

                // Insert new subjects
                DB::table('exam_form_subjects')->insert($examFormSubjects);
            });

            // Return success response
            return redirect()->route('student.dashboard')->with(['success' => 'Exam form and subjects saved successfully!']);
        } catch (QueryException $e) {
            // Handle query exception
            return redirect()->back()->with(['error' => 'Failed to save exam form or subjects.']);
        } catch (\Exception $e) {
            // Handle general exception
            return redirect()->back()->with(['error' => 'An unexpected error occurred.' . $e->getMessage()]);
        }
    }
    public function admitcard_form_list()
    {
        $admitcardSession = ExamSession::where('status', 'admit-card')->get();
        return view('student.semester.admitcard-form-list', compact('admitcardSession'));
    }

    public function admitcard_download($exam_session_id)
    {
        $examform = ExamForm::with(['subjects', 'student'])->where('student_id', auth()->guard('student')->id())->where('session_id', $exam_session_id)->first();
        $arrView['student'] = $examform->student;
        $arrView['subjects'] = $examform->subjects;
        return view('student.semester.admitcard', $arrView);
    }
    public function locked_subject_by_examsession($exam_session_id){
        $locked_subjects=ExamForm::with(['subjects'])->where('session_id',$exam_session_id)->where('student_id',auth()->guard('student')->id())->first();
        $html=" <table class='table table-responsive table-bordered'>
            <tr>
            <th>Sr No</th>
            <th>Subject Code</th>
            <th>Subject Name</th>
            </tr>";
            $index = 1; // Start the counter at 1
        foreach($locked_subjects->subjects as $subject){
            $html .="<tr>
                <td>{$index}</td>
                <td>{$subject->subject_code}</td>
                <td>" . ucfirst($subject->title) . "</td>
            </tr>";
            $index++;
        }
        $html .=`</table>`;
        return $html;
    }
}
