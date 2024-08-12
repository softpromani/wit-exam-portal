<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamFormApplicationReq;
use App\Models\ExamForm;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Subject;
use DB;
class ExamFormController extends Controller
{
    function exam_form(){
        if(auth()->guard('student')->user()->checkThisSemFormStatus()){
            return redirect()->back()->with(['warning'=>'Your Exam Form for  this Session already submitted']);
        }
        $student=auth()->guard('student')->user();
        return view('student.semester.exam-form',compact('student'));
    }

    public function subject_fetch(Request $request){
      $subjects = Subject::select('id','subject_code','title')->where('subject_code', 'LIKE', '%' . $request->param . '%')->orWhere('title', 'LIKE','%'.$request->param.'%')
      ->limit(10)
      ->get();
        return response()->json($subjects,200);
    }
    public function apply_exam_form(ExamFormApplicationReq $req){
            $data=$req->validated();
            $data['session_id']=1;
            $data['semester_id']=auth()->guard('student')->user()->semester_id;
            $data['student_id']=auth()->guard('student')->id();
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
                return redirect()->back()->with(['error' => 'An unexpected error occurred.'.$e->getMessage()]);
            }
    }
}
