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
        $query = ExamForm::query();
        $paymentQuery = Payment::where('payment_status', 'paid');

        // Filter by Exam Session if selected
        if ($request->has('exam_session_id') && $request->exam_session_id != '') {
            $query->where('session_id', $request->exam_session_id);
            // Filter payments related to exam forms in that session
            $paymentQuery->whereHasMorph('paymentable', [ExamForm::class], function ($q) use ($request) {
                $q->where('session_id', $request->exam_session_id);
            });
        }

        $examsession = ExamSession::get();
        $examFormCount = $query->count();
        $paystatus = $paymentQuery->count();
        $totalFee = $paymentQuery->sum('paid_amount');


        // --- Chart Data Preparation ---

        // 1. Exam Forms Status Distribution (e.g., Submitted, Approved, etc. - assuming 'exam_status' or 'result_status')
        // Let's use 'payment_status' for a pie chart as requested/implied by "Paid Fee Status"
        // But let's look at what data we have. We have ExamForm count and Payment Status.
        // Let's make a chart for "Admissions per Course" (ExamForms per Course for the selected session).

        $admissionsPerCourse = $query->with('student.course')
            ->get()
            ->groupBy('student.course.name')
            ->map(function ($row) {
                return $row->count();
            });

        $courseNames = $admissionsPerCourse->keys();
        $courseCounts = $admissionsPerCourse->values();

        // 2. Payment Status (Paid vs Total/Unpaid)
        // We have $paystatus (Paid count). Total forms is $examFormCount.
        // Unpaid/Pending = $examFormCount - $paystatus.
        $unpaidCount = $examFormCount - $paystatus;

        return view('admin.dashboard', compact(
            'examFormCount',
            'paystatus',
            'totalFee',
            'examsession',
            'courseNames',
            'courseCounts',
            'unpaidCount'
        ));
    }
}
