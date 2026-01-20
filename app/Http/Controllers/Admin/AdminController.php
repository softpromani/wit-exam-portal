<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExamForm;
use App\Models\ExamSession;
use App\Models\Payment;

class AdminController extends Controller
{
    //
    public function adminDashboard(Request $request)
    {
        // --- 1. Fetch Filter Data ---
        $examsession = ExamSession::get();
        // Assuming models are imported or aliased.
        $courses = \App\Models\Course::with('branches')->get();
        $semesters = \App\Models\Semester::get();
        $admission_sessions = \App\Models\AdmissionSession::get();

        // --- 2. Base Queries ---
        $studentQuery = \App\Models\Student::query();
        $examFormQuery = ExamForm::query();
        $paymentQuery = Payment::where('payment_status', 'paid');

        // --- 3. Apply Filters ---

        // Filter: Exam Session (Applies to ExamForm and Payment)
        if ($request->has('exam_session_id') && $request->exam_session_id != '') {
            $examFormQuery->where('session_id', $request->exam_session_id);
            $paymentQuery->whereHasMorph('paymentable', [ExamForm::class], function ($q) use ($request) {
                $q->where('session_id', $request->exam_session_id);
            });
        }

        // Filter: Course / Branch
        if ($request->has('branch_id') && $request->branch_id != '') {
            $studentQuery->where('branch_id', $request->branch_id);
            $examFormQuery->whereHas('student', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
            $paymentQuery->whereHasMorph('paymentable', [ExamForm::class], function ($q) use ($request) {
                $q->whereHas('student', function ($sq) use ($request) {
                    $sq->where('branch_id', $request->branch_id);
                });
            });
        } elseif ($request->has('course_id') && $request->course_id != '') { // If course filter existed separately
            $studentQuery->where('course_id', $request->course_id);
            $examFormQuery->whereHas('student', function ($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
            $paymentQuery->whereHasMorph('paymentable', [ExamForm::class], function ($q) use ($request) {
                $q->whereHas('student', function ($sq) use ($request) {
                    $sq->where('course_id', $request->course_id);
                });
            });
        }

        // Filter: Semester
        if ($request->has('semester_id') && $request->semester_id != '') {
            $studentQuery->where('semester_id', $request->semester_id);
            $examFormQuery->where('semester_id', $request->semester_id);
            $paymentQuery->whereHasMorph('paymentable', [ExamForm::class], function ($q) use ($request) {
                $q->where('semester_id', $request->semester_id);
            });
        }

        // Filter: Admission Session
        if ($request->has('admission_session_id') && $request->admission_session_id != '') {
            $studentQuery->where('admission_session_id', $request->admission_session_id);
            $examFormQuery->whereHas('student', function ($q) use ($request) {
                $q->where('admission_session_id', $request->admission_session_id);
            });
            $paymentQuery->whereHasMorph('paymentable', [ExamForm::class], function ($q) use ($request) {
                $q->whereHas('student', function ($sq) use ($request) {
                    $sq->where('admission_session_id', $request->admission_session_id);
                });
            });
        }

        // --- 4. Execute Queries & Aggregates ---

        // Counts
        $examFormCount = $examFormQuery->count();
        $paystatus = $paymentQuery->count();
        $totalFee = $paymentQuery->sum('paid_amount');
        $studentCount = $studentQuery->count();
        $unpaidCount = $examFormCount - $paystatus;

        // Chart Data 1: Exam Forms per Course
        $admissionsPerCourse = (clone $examFormQuery)->with('student.course')
            ->get()
            ->groupBy('student.course.name')
            ->map(function ($row) {
                return $row->count();
            });
        $courseNames = $admissionsPerCourse->keys();
        $courseCounts = $admissionsPerCourse->values();

        // Chart Data 2: Students per Branch
        $studentsPerBranch = (clone $studentQuery)->with('branch')
            ->get()
            ->groupBy('branch.name')
            ->map(function ($row) {
                return $row->count();
            });
        $branchNames = $studentsPerBranch->keys();
        $branchCounts = $studentsPerBranch->values();

        // Chart Data 3: Students per Admission Session
        $studentsPerSession = (clone $studentQuery)->with('admission_session')
            ->get()
            ->groupBy('admission_session.session_name')
            ->map(function ($row) {
                return $row->count();
            });
        $sessionNames = $studentsPerSession->keys();
        $sessionCounts = $studentsPerSession->values();

        // Chart Data 4: Students per Semester
        $studentsPerSemester = (clone $studentQuery)->with('semester')
            ->get()
            ->groupBy('semester.semester_name')
            ->map(function ($row) {
                return $row->count();
            });
        $semesterNames = $studentsPerSemester->keys();
        $semesterCounts = $studentsPerSemester->values();


        return view('admin.dashboard', compact(
            'examsession',
            'courses',
            'semesters',
            'admission_sessions', // Lists
            'examFormCount',
            'paystatus',
            'totalFee',
            'studentCount',
            'unpaidCount', // Counts
            'courseNames',
            'courseCounts', // Chart 1
            'branchNames',
            'branchCounts', // Chart 2
            'sessionNames',
            'sessionCounts', // Chart 3
            'semesterNames',
            'semesterCounts' // Chart 4
        ));
    }
}
