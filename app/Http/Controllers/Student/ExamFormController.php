<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamFormApplicationReq;
use App\Models\ExamForm;
use App\Models\ExamSchedule;
use App\Models\ExamSession;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Subject;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Storage;

class ExamFormController extends Controller
{
    function exam_form()
    {
        $student = auth()->guard('student')->user(); // Get the authenticated student once

        $examSessions = ExamSession::where('status', 'proccess')->get()->map(function ($examSession) use ($student) {
            // Get the form for this session
            $examForm = ExamForm::where('student_id', $student->id)
                ->where('semester_id', $student->semester_id)
                ->where('session_id', $examSession->id)
                ->whereNot('exam_status', 'cancel')
                ->first();

            // Add the form status as a new attribute to the exam session
            $examSession->form_status = $examForm ? true : false;

            $paymentStatus = 'pending';
            if ($examForm) {
                if ($examForm->payment_status === 'done') {
                    $paymentStatus = 'done';
                } else {
                    // Sync from transactions if not already marked as done
                    $hasSuccess = $examForm->transactions()
                        ->whereIn('status', ['success', 'completed'])
                        ->exists();
                    if ($hasSuccess) {
                        $paymentStatus = 'done';
                        $examForm->update(['payment_status' => 'done']);
                    }
                }
            }
            $examSession->payment_status = $paymentStatus;
            $examSession->exam_form_id = $examForm ? $examForm->id : null;

            // Return the modified exam session with the new key
            return $examSession;
        });
        return view('student.semester.exam-form-list', ['examSessions' => $examSessions]);
    }
    function apply_for_exam($exam_session_id, $edit = NULL)
    {
        $locked_subjects = [];
        if ($edit != true) {
            if (auth()->guard('student')->user()->checkThisSemFormStatus($exam_session_id)) {
                return redirect()->route('student.semester.exam-form')->with(['warning' => 'Your Exam Form for  this Session already submitted']);
            }
        } else {
            $locked_subjects = optional(ExamForm::with(['subjects'])->where('session_id', $exam_session_id)->where('student_id', auth()->guard('student')->id())->first())->subjects;
        }

        $student = auth()->guard('student')->user();
        return view('student.semester.exam-form', compact('student', 'exam_session_id', 'locked_subjects'));
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
            $examFormId = null;

            DB::transaction(function () use ($data, &$examFormId) {

                $examForm = ExamForm::firstOrCreate(
                    [
                        'session_id' => $data['session_id'],
                        'semester_id' => $data['semester_id'],
                        'student_id' => $data['student_id']
                    ],
                    $data
                );

                $examFormId = $examForm->id;

                $examFormSubjects = array_map(function ($subjectId) use ($examForm) {
                    return [
                        'exam_form_id' => $examForm->id,
                        'subject_id' => $subjectId,
                        'total_marks' => 0,
                        'obtain_marks' => 0,
                        'grade' => null,
                    ];
                }, $data['choosen_subjects']);

                DB::table('exam_form_subjects')
                    ->where('exam_form_id', $examForm->id)
                    ->delete();

                DB::table('exam_form_subjects')->insert($examFormSubjects);
            });

            return redirect()
                ->route('student.payment.process', ['examFormId' => $examFormId])
                ->with('success', 'Exam form and subjects saved successfully!');

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
        $examform = ExamForm::with(['subjects', 'student', 'exam_session'])
            ->where('student_id', auth()->guard('student')->id())
            ->where('session_id', $exam_session_id)
            ->first();

        // Check if exam form exists
        if (!$examform) {
            // Handle case when no exam form is found
            return response()->json(['message' => 'No exam form found.'], 404);
        }
        $arrView['examSession'] = $examform->exam_session;
        $arrView['student'] = $examform->student;


        $arrView['subjects'] = $examform->subjects->map(function ($subject) use ($examform) {
            $schedule = ExamSchedule::where('exam_session_id', $examform->session_id)
                ->where('subject_id', $subject->id)
                ->first();
            $subject->date = Carbon::parse(optional($schedule)->date)->format('d-M-Y');
            $startDate = Carbon::parse(optional($schedule)->from_time ? optional($schedule)->from_time : '00:00:00')->format('h:i a');
            $endDate = Carbon::parse(optional($schedule)->to_time ? optional($schedule)->to_time : '00:00:00')->format('h:i a');
            $subject->time = $startDate . ' to ' . $endDate;
            return $subject;
        })->sortBy('date');
        return view('student.semester.admitcard', $arrView);
    }

    public function examresult_form_list()
    {
        $pdfresult = false;
        $universityRollNo = auth()->user()->university_roll_no; // Example: 'cs-123456'
        $numericRollNo = preg_replace('/[^0-9]/', '', $universityRollNo);

        if (Storage::exists('result/' . $numericRollNo . '.pdf')) {
            $pdfresult = $numericRollNo;

        }
        $examSession = ExamSession::where('status', 'admit-card')->get();
        return view('student.semester.exam-result-list', compact('examSession', 'pdfresult'));
    }

    public function examresult_download($exam_session_id)
    {
        $examform = ExamForm::with(['subjects', 'student', 'examfrom_has_subjects'])
            ->where('student_id', auth()->guard('student')->id())
            ->where('session_id', $exam_session_id)
            ->first();

        // Check if exam form exists
        if (!$examform) {
            // Handle case when no exam form is found
            return response()->json(['message' => 'No exam form found.'], 404);
        }

        $arrView['student'] = $examform->student;

        $arrView['subjects'] = $examform->subjects->map(function ($subject) use ($examform) {
            // Get the schedule for the subject
            $schedule = ExamSchedule::where('exam_session_id', $examform->session_id)
                ->where('subject_id', $subject->id)
                ->first();

            // Find the corresponding ExamFormSubject for total_marks
            $examFormSubject = $examform->examfrom_has_subjects->where('subject_id', $subject->id)->first();

            // Add total_marks to the subject
            $subject->grade_point = optional($examFormSubject)->grade_point;
            $subject->grade = optional($examFormSubject)->grade;

            // Format date and time
            $subject->date = Carbon::parse(optional($schedule)->date)->format('d-M-Y');
            $startDate = Carbon::parse(optional($schedule)->from_time ?: '00:00:00')->format('h:i a');
            $endDate = Carbon::parse(optional($schedule)->to_time ?: '00:00:00')->format('h:i a');
            $subject->time = $startDate . ' to ' . $endDate;

            return $subject;
        })->sortBy('date');

        // dd($arrView);
        return view('student.semester.examresult', $arrView);
    }


    public function locked_subject_by_examsession($exam_session_id)
    {
        $locked_subjects = ExamForm::with(['subjects'])->where('session_id', $exam_session_id)->where('student_id', auth()->guard('student')->id())->first();
        $html = " <table class='table table-responsive table-bordered'>
            <tr>
            <th>Sr No</th>
            <th>Subject Code</th>
            <th>Subject Name</th>
            </tr>";
        $index = 1; // Start the counter at 1
        foreach ($locked_subjects->subjects as $subject) {
            $html .= "<tr>
                <td>{$index}</td>
                <td>{$subject->subject_code}</td>
                <td>" . ucfirst($subject->title) . "</td>
            </tr>";
            $index++;
        }
        $html .= "</table>";
        return $html;
    }

    public function locked_payment_history($exam_session_id)
    {
        $examForm = ExamForm::with([
            'transactions' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])->where('session_id', $exam_session_id)
            ->where('student_id', auth()->guard('student')->id())
            ->first();

        if (!$examForm) {
            return "No exam form found for this session.";
        }

        $html = " <table class='table table-responsive table-bordered'>
            <thead class='table-info'>
                <tr>
                    <th>Txn ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>";

        foreach ($examForm->transactions as $txn) {
            $statusBadge = match ($txn->status) {
                'success', 'completed' => '<span class="badge badge-success">Success</span>',
                'failed' => '<span class="badge badge-danger">Failed</span>',
                'pending', 'initiated', 'processing' => '<span class="badge badge-warning">' . ucfirst($txn->status) . '</span>',
                default => '<span class="badge badge-secondary">' . ucfirst($txn->status) . '</span>',
            };

            $recheckBtn = '';
            if (!in_array($txn->status, ['success', 'completed'])) {
                $recheckBtn = "<button class='btn btn-sm btn-info recheckStatusBtn' data-id='{$txn->transaction_id}'>Recheck</button>";
            }

            $date = Carbon::parse($txn->created_at)->format('d-M-Y H:i');
            $html .= "<tr>
                <td>{$txn->transaction_id}</td>
                <td>{$txn->amount}</td>
                <td>{$statusBadge}</td>
                <td>{$date}</td>
                <td>{$recheckBtn}</td>
            </tr>";
        }

        if ($examForm->transactions->isEmpty()) {
            $html .= "<tr><td colspan='5' class='text-center'>No transactions found.</td></tr>";
        }

        $html .= "</tbody></table>";
        return $html;
    }
}
