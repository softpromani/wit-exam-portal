<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use Illuminate\Http\Request;

class ExamSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $examsessions=ExamSession::get();
        return view('admin.session.exam-session',compact('examsessions'));

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
            'exam_center'=>'required',
        ]);

        $data = [
            //Database column_name => Form field name
            'session_name' => $request->session_name,
            'from' => $request->from,
            'to' => $request->to,
            'status'=>'proccess',
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
        return view('admin.session.exam-session', compact('editexamsession','examsessions'));
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
    public function destroy(ExamSession $examsession )
    {
        $examsession->delete();
        return redirect()->route('admin.exam-session.destroy');
    }
}
