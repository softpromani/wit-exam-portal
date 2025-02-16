<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionSession;
use Illuminate\Http\Request;

class AddmissionSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {  $admissionsessions=AdmissionSession::get();
       return view('admin.session.admission-session',compact('admissionsessions'));
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
        ]);

        $data = [
            //Database column_name => Form field name
            'session_name' => $request->session_name,
            'from' => $request->from,
            'to' => $request->to,
         
        ];

        $admissionsession = AdmissionSession::create($data);
        return redirect()->route('admin.admission-session.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(AdmissionSession $admissionsession)
    // {
    //     $editadmissionsession = $admissionsession;
    //     // dd($edituser);
    //     $admissionsessions = AdmissionSession::get();
    //     return view('admin.session.admission-session', compact('editadmissionsession','admissionsessions'));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, AdmissionSession $admissionsession)
    // {
    //   // dd($request->all());
    // $data = [
    //     //Database column_name => Form field name
    //     'session_name' => $request->session_name,
    //     'from' => $request->to,
    //     'to' => $request->to,
    // ];

    // $admissionsession = AdmissionSession::find($admissionsession->id)->update($data);
    // return redirect()->route('admin.admission-session.update');
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(AdmissionSession $admissionsession )
    // {
    //     $admissionsession->delete();
    //     return redirect()->route('admin.admission-session.destroy');
    // }
}
