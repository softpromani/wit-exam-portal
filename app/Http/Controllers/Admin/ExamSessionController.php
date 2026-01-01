<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\Course;
use App\Models\Branch;
use App\Models\Semester;
use App\Models\ExamSessionHasCBS;
use Illuminate\Http\Request;
use DB;

class ExamSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $examsessions = ExamSession::with('exam_session_has_cbs.course', 'exam_session_has_cbs.branch')->get();
        return view('admin.session.exam-session', compact('examsessions'));
    }

    public function setAmount($id)
    {
        $examsession = ExamSession::findOrFail($id);
        $courses = Course::all();
        $branches = Branch::all();
        $semesters = Semester::all();
        $existingConfigs = ExamSessionHasCBS::with(['course', 'branch'])->where('exam_session_id', $id)->get();

        return view('admin.session.set-amount', compact('examsession', 'courses', 'branches', 'semesters', 'existingConfigs'));
    }

    public function storeAmount(Request $request)
    {
        $request->validate([
            'exam_session_id' => 'required|exists:exam_sessions,id',
            'course_id' => 'required',
            'branch_id' => 'required',
            'semesters' => 'required|array',
            'amounts' => 'required|array',
        ]);

        $examsession = ExamSession::findOrFail($request->exam_session_id);
        $course_id = $request->course_id;
        $branch_id = $request->branch_id;
        $semesters = array_values(array_filter($request->semesters)); // array of semester IDs
        $amounts = array_values(array_filter($request->amounts)); // array of amounts corresponding to semesters

        $semester_amounts = [];
        foreach ($request->semesters as $index => $semId) {
            if ($semId && isset($request->amounts[$index]) && $request->amounts[$index] > 0) {
                $semester_amounts[$semId] = $request->amounts[$index];
            }
        }

        ExamSessionHasCBS::updateOrCreate(
            [
                'exam_session_id' => $examsession->id,
                'course_id' => $course_id,
                'branch_id' => $branch_id,
            ],
            [
                'semesters' => array_keys($semester_amounts),
                'amounts' => array_values($semester_amounts),
                'semester_amounts' => $semester_amounts
            ]
        );

        return redirect()->back()->with('success', 'Amount configured successfully');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'session_name' => 'required|string|unique:admission_sessions,session_name|max:255',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
            'exam_center' => 'required',
        ]);

        $data = [
            //Database column_name => Form field name
            'session_name' => $request->session_name,
            'from' => $request->from,
            'to' => $request->to,
            'status' => 'proccess',
            'exam_center' => $request->exam_center,

        ];

        $examsession = ExamSession::create($data);
        return redirect()->route('admin.exam-session.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamSession $examsession)
    {
        $editexamsession = $examsession->first();
        // dd($edituser);
        $examsessions = ExamSession::get();
        return view('admin.session.exam-session', compact('editexamsession', 'examsessions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamSession $examsession)
    {
        // dd($request->all());
        $data = [
            //Database column_name => Form field name
            'session_name' => $request->session_name,
            'from' => $request->to,
            'to' => $request->to,
            'exam_center' => $request->exam_center,
        ];

        $examsession = ExamSession::find($examsession->id)->update($data);
        return redirect()->route('admin.exam-session.update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamSession $examsession)
    {
        $examsession->delete();
        return redirect()->route('admin.exam-session.destroy');
    }

    public function updateStatus(Request $request, $id)
    {
        $session = ExamSession::find($id);
        if ($session) {
            $session->status = $request->status;
            $session->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
}
