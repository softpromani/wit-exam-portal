<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionSession;
use App\Models\Branch;
use App\Models\Course;
use App\Models\ImportFail;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Promotion;
use App\Models\PromotionStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentController extends Controller
{
    public function index()
    {
        $courses = Course::with('branches')->get();
        $admission_sessions = AdmissionSession::latest()->pluck('session_name', 'id')->toArray();
        return view('admin.student.bulk-upload', compact('courses', 'admission_sessions'));
    }
    public function import(Request $request)
    {
        $request->validate([
            'registration_type' => 'required|in:normal,lateral',
            'branch_id' => 'required|exists:branches,id',
            'admission_session_id' => 'required|exists:admission_sessions,id',
            'excel' => 'required|file|mimes:xlsx,csv,txt,xls|max:102400'
        ]);

        @ini_set('max_execution_time', 0);
        @set_time_limit(0);
        @ini_set('memory_limit', '1024M');

        // Fetch course_id from branches table
        $branch = Branch::findOrFail($request->branch_id);
        $course = $branch->course; // Get the associated course_id

        // Generate a unique filename
        $extension = $request->file('excel')->getClientOriginalExtension();
        $fileName = $this->generateUniqueFileName($request->registration_type, $request->branch_id, $request->admission_session_id, $extension);

        // Store the file
        $filePath = $request->file('excel')->storeAs('uploads/students', $fileName);

        // Process Excel/CSV File with high-performance streaming & batch insert
        $res = $this->processExcel(storage_path("app/$filePath"), $fileName, $request, $course, $branch);
        return back()->with('success', 'File processed successfully. ' . ($res['success'] ?? 0) . ' students imported and ' . ($res['fail'] ?? 0) . ' failed.');
    }

    private function generateUniqueFileName($registrationType, $branchId, $admissionSessionId, $extension)
    {
        $baseFileName = "{$registrationType}@{$branchId}@{$admissionSessionId}";
        $fileName = $baseFileName . ".$extension";
        $counter = 1;

        // Check if file exists and append _01, _02, etc.
        while (Storage::exists("uploads/students/$fileName")) {
            $fileName = "{$baseFileName}_" . str_pad($counter, 2, '0', STR_PAD_LEFT) . ".$extension";
            $counter++;
        }

        return $fileName;
    }

    private function processExcel(string $filePath, string $fileName, Request $request, object $course, object $branch)
    {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);
        @ini_set('memory_limit', '1024M');

        $branchCode = strtoupper(substr($branch->name, 0, 3));
        $session = AdmissionSession::find($request->admission_session_id);
        $yearCode = $session && $session->from ? date('y', strtotime($session->from)) : date('y');

        // Pre-calculate starting counters once outside the row loop to avoid thousands of DB queries
        $currentRollCounter = Student::where('branch_id', $branch->id)
            ->where('admission_session_id', $session->id)
            ->count();

        $maxReg = Student::max('registration_no');
        $currentRegCounter = $maxReg ? (int)$maxReg : (int)($yearCode . '00000000');

        $passwordCache = [];
        $success_count = 0;
        $fail_count = 0;
        $studentBatch = [];
        $now = now();

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($ext === 'csv' || $ext === 'txt') {
            if (($handle = fopen($filePath, 'r')) !== false) {
                $headerRow = fgetcsv($handle);
                if (!$headerRow) {
                    fclose($handle);
                    return ['success' => 0, 'fail' => 0];
                }

                $headers = array_map(function ($h) {
                    return strtolower(trim((string)$h));
                }, $headerRow);

                $nameIndex = array_search('name', $headers);
                $passwordIndex = array_search('password', $headers);
                $semesterIndex = array_search('semester', $headers);

                if ($nameIndex === false || $passwordIndex === false || $semesterIndex === false) {
                    ImportFail::create([
                        'file_name' => $fileName,
                        'row_data' => json_encode(['error' => 'Missing required columns: name, password, semester']),
                    ]);
                    fclose($handle);
                    return ['success' => 0, 'fail' => 1];
                }

                while (($row = fgetcsv($handle)) !== false) {
                    if (empty($row) || (count($row) === 1 && $row[0] === null)) {
                        continue;
                    }

                    $studentName = trim((string)($row[$nameIndex] ?? ''));
                    $rawPassword = (string)($row[$passwordIndex] ?? '123456');
                    $semesterId = (int)($row[$semesterIndex] ?? 1);

                    if ($studentName === '') {
                        $fail_count++;
                        ImportFail::create([
                            'file_name' => $fileName,
                            'row_data' => json_encode($row),
                            'reason' => 'Empty student name',
                        ]);
                        continue;
                    }

                    if (!isset($passwordCache[$rawPassword])) {
                        $passwordCache[$rawPassword] = bcrypt($rawPassword);
                    }

                    $currentRollCounter++;
                    $currentRegCounter++;
                    $universityRollNo = sprintf("%s-%s-%03d", $branchCode, $yearCode, $currentRollCounter);
                    $regNo = $currentRegCounter;

                    $studentBatch[] = [
                        'university_roll_no' => $universityRollNo,
                        'registration_no' => $regNo,
                        'student_name' => $studentName,
                        'semester_id' => $semesterId,
                        'admission_semester_id' => $semesterId,
                        'password' => $passwordCache[$rawPassword],
                        'branch_id' => $branch->id,
                        'course_id' => $course->id,
                        'admission_session_id' => $session->id,
                        'registration_type' => $request->registration_type,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if (count($studentBatch) >= 1000) {
                        Student::insert($studentBatch);
                        $success_count += count($studentBatch);
                        $studentBatch = [];
                    }
                }
                fclose($handle);
            }
        } else {
            // Read-only spreadsheet parsing for xlsx/xls
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                return ['success' => 0, 'fail' => 0];
            }

            $headers = array_map(function ($h) {
                return strtolower(trim((string)$h));
            }, $rows[0]);

            $nameIndex = array_search('name', $headers);
            $passwordIndex = array_search('password', $headers);
            $semesterIndex = array_search('semester', $headers);

            if ($nameIndex === false || $passwordIndex === false || $semesterIndex === false) {
                ImportFail::create([
                    'file_name' => $fileName,
                    'row_data' => json_encode(['error' => 'Missing required columns: name, password, semester']),
                ]);
                return ['success' => 0, 'fail' => 1];
            }

            foreach (array_slice($rows, 1) as $row) {
                $studentName = trim((string)($row[$nameIndex] ?? ''));
                $rawPassword = (string)($row[$passwordIndex] ?? '123456');
                $semesterId = (int)($row[$semesterIndex] ?? 1);

                if ($studentName === '') {
                    $fail_count++;
                    ImportFail::create([
                        'file_name' => $fileName,
                        'row_data' => json_encode($row),
                        'reason' => 'Empty student name',
                    ]);
                    continue;
                }

                if (!isset($passwordCache[$rawPassword])) {
                    $passwordCache[$rawPassword] = bcrypt($rawPassword);
                }

                $currentRollCounter++;
                $currentRegCounter++;
                $universityRollNo = sprintf("%s-%s-%03d", $branchCode, $yearCode, $currentRollCounter);
                $regNo = $currentRegCounter;

                $studentBatch[] = [
                    'university_roll_no' => $universityRollNo,
                    'registration_no' => $regNo,
                    'student_name' => $studentName,
                    'semester_id' => $semesterId,
                    'admission_semester_id' => $semesterId,
                    'password' => $passwordCache[$rawPassword],
                    'branch_id' => $branch->id,
                    'course_id' => $course->id,
                    'admission_session_id' => $session->id,
                    'registration_type' => $request->registration_type,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($studentBatch) >= 1000) {
                    Student::insert($studentBatch);
                    $success_count += count($studentBatch);
                    $studentBatch = [];
                }
            }
            unset($spreadsheet);
        }

        if (!empty($studentBatch)) {
            Student::insert($studentBatch);
            $success_count += count($studentBatch);
            $studentBatch = [];
        }

        return ['success' => $success_count, 'fail' => $fail_count];
    }
    public function generateRegistrationNo(int $admission_session_id)
    {
        // Ensure there is at least one counter record
        $counter = student::max('registration_no');

        $trimmedCounter = substr($counter, 2);
        $session = AdmissionSession::find($admission_session_id);
        $yearPrefix = date('y', strtotime($session->from)); // Extract last 2 digits of the year

        // Generate new registration number
        $newRegistrationNo = $yearPrefix . $trimmedCounter + 1;

        return $newRegistrationNo;
    }
    function generateUniversityRollNo($branch_id, $admission_session_id)
    {
        // Get the branch name and extract the first 3 letters
        $branch = Branch::find($branch_id);
        $branchCode = strtoupper(substr($branch->name, 0, 3)); // Ensure uppercase

        // Get the admission session and extract last 2 digits of `from_date`
        $session = AdmissionSession::find($admission_session_id);
        $yearCode = date('y', strtotime($session->from)); // Extract last 2 digits of the year

        // Count existing students for the branch and session to get incremented value
        $studentCount = Student::where('branch_id', $branch_id)
            ->where('admission_session_id', $admission_session_id)
            ->count() + 1; // Increment by 1

        // Format the roll number (e.g., CSE-24-001)
        $universityRollNo = sprintf("%s-%s-%03d", $branchCode, $yearCode, $studentCount);

        return $universityRollNo;
    }


    // get all student list with server-side pagination & high-volume SQL aggregates
    public function getStudents(Request $request)
    {
        $globalPaidStudentsCount = Student::whereHas('examForms.payment', function ($q) {
            $q->where('payment_status', 'paid');
        })->count();
        $globalCollectedFee = (float) Payment::where('payment_status', 'paid')->sum('paid_amount');
        $globalTotalStudents = Student::count();
        $globalCompletedStudents = Student::where('is_profile', 1)->count();
        $globalIncompleteStudents = Student::where(function ($q) {
            $q->where('is_profile', 0)->orWhereNull('is_profile');
        })->count();

        $arrView = [
            'courses' => Course::with('branches')->get(),
            'semesters' => Semester::get(),
            'admission_sessions' => AdmissionSession::get(),
            'globalTotalStudents' => $globalTotalStudents,
            'globalCompletedStudents' => $globalCompletedStudents,
            'globalIncompleteStudents' => $globalIncompleteStudents,
            'globalPaidStudents' => $globalPaidStudentsCount,
            'globalCollectedFee' => $globalCollectedFee,
        ];

        $hasFilters = $request->isMethod('post')
            || $request->has('is_profile')
            || $request->has('fee_status')
            || $request->has('branch_id')
            || $request->has('semester_id')
            || $request->has('admission_session_id')
            || $request->has('course_id')
            || $request->has('university_roll_no')
            || $request->has('registration_no')
            || $request->has('student_name')
            || $request->has('fetch')
            || $request->has('page');

        if ($hasFilters) {
            $query = Student::query();

            // Apply search filters
            if ($request->filled('university_roll_no')) {
                $query->where('university_roll_no', 'like', '%' . $request->university_roll_no . '%');
            }
            if ($request->filled('registration_no')) {
                $query->where('registration_no', 'like', '%' . $request->registration_no . '%');
            }
            if ($request->filled('registration_type')) {
                $query->where('registration_type', 'like', '%' . $request->registration_type . '%');
            }
            if ($request->filled('student_name')) {
                $query->where('student_name', 'like', '%' . $request->student_name . '%');
            }
            if ($request->filled('course_id')) {
                $query->where('course_id', $request->course_id);
            }
            if ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }
            if ($request->filled('semester_id')) {
                $query->where('semester_id', $request->semester_id);
            }
            if ($request->filled('admission_session_id')) {
                $query->where('admission_session_id', $request->admission_session_id);
            }
            if ($request->has('is_profile') && $request->is_profile !== '' && $request->is_profile !== null) {
                if ($request->is_profile == '1') {
                    $query->where('is_profile', 1);
                } else {
                    $query->where(function ($q) {
                        $q->where('is_profile', 0)->orWhereNull('is_profile');
                    });
                }
            }
            if ($request->filled('fee_status')) {
                if ($request->fee_status === 'paid') {
                    $query->whereHas('examForms.payment', function ($q) {
                        $q->where('payment_status', 'paid');
                    });
                } elseif ($request->fee_status === 'unpaid') {
                    $query->whereDoesntHave('examForms.payment', function ($q) {
                        $q->where('payment_status', 'paid');
                    });
                }
            }

            // High-performance SQL aggregates for the filtered cohort
            $arrView['filteredCount'] = (clone $query)->count();
            $arrView['filteredCompleted'] = (clone $query)->where('is_profile', 1)->count();
            $arrView['filteredIncomplete'] = $arrView['filteredCount'] - $arrView['filteredCompleted'];

            $matchingStudentIds = (clone $query)->select('id');
            $arrView['filteredCollectedFee'] = (float) Payment::where('payment_status', 'paid')
                ->where('paymentable_type', \App\Models\ExamForm::class)
                ->whereIn('paymentable_id', function ($sub) use ($matchingStudentIds) {
                    $sub->select('id')->from('exam_forms')->whereIn('student_id', $matchingStudentIds);
                })->sum('paid_amount');

            $arrView['filteredPaidStudents'] = (clone $query)->whereHas('examForms.payment', function ($q) {
                $q->where('payment_status', 'paid');
            })->count();

            // Pagination configuration
            $perPage = (int) $request->input('per_page', 50);
            if ($perPage < 10) $perPage = 10;
            if ($perPage > 500) $perPage = 500;
            $arrView['perPage'] = $perPage;

            // Eager-loaded paginated student collection
            $students = $query->with([
                'semester',
                'branch',
                'admission_session',
                'admission_semester',
                'examForms' => function ($q) {
                    $q->latest();
                },
                'examForms.payment'
            ])->paginate($perPage)->withQueryString();

            $arrView['students'] = $students;
        }

        return view('admin.student.list', $arrView);
    }



    public function promote(Request $req)
    {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);
        @ini_set('memory_limit', '1024M');

        $promoteAllFiltered = $req->boolean('promote_all_filtered');

        if ($promoteAllFiltered) {
            $query = Student::query();

            if ($req->filled('university_roll_no')) {
                $query->where('university_roll_no', 'like', '%' . $req->university_roll_no . '%');
            }
            if ($req->filled('registration_no')) {
                $query->where('registration_no', 'like', '%' . $req->registration_no . '%');
            }
            if ($req->filled('registration_type')) {
                $query->where('registration_type', 'like', '%' . $req->registration_type . '%');
            }
            if ($req->filled('student_name')) {
                $query->where('student_name', 'like', '%' . $req->student_name . '%');
            }
            if ($req->filled('course_id')) {
                $query->where('course_id', $req->course_id);
            }
            if ($req->filled('branch_id')) {
                $query->where('branch_id', $req->branch_id);
            }
            if ($req->filled('semester_id')) {
                $query->where('semester_id', $req->semester_id);
            }
            if ($req->filled('admission_session_id')) {
                $query->where('admission_session_id', $req->admission_session_id);
            }
            if ($req->has('is_profile') && $req->is_profile !== '' && $req->is_profile !== null) {
                if ($req->is_profile == '1') {
                    $query->where('is_profile', 1);
                } else {
                    $query->where(function ($q) {
                        $q->where('is_profile', 0)->orWhereNull('is_profile');
                    });
                }
            }
            if ($req->filled('fee_status')) {
                if ($req->fee_status === 'paid') {
                    $query->whereHas('examForms.payment', function ($q) {
                        $q->where('payment_status', 'paid');
                    });
                } elseif ($req->fee_status === 'unpaid') {
                    $query->whereDoesntHave('examForms.payment', function ($q) {
                        $q->where('payment_status', 'paid');
                    });
                }
            }

            $studentsToPromote = $query->select('id', 'semester_id')->get();
        } else {
            $req->validate([
                'selected_students' => 'required|array',
                'selected_students.*' => 'exists:students,id',
            ]);
            $studentIds = $req->input('selected_students');
            $studentsToPromote = Student::whereIn('id', $studentIds)->select('id', 'semester_id')->get();
        }

        if ($studentsToPromote->isEmpty()) {
            return redirect()->back()->with('error', 'No eligible students selected or found for promotion.');
        }

        $allSemesters = Semester::all()->keyBy('id');
        $maxSemId = $allSemesters->keys()->max() ?? 8;
        $batchNo = 'PROM-' . date('YmdHis') . '-' . strtoupper(Str::random(4));
        $now = now();

        DB::beginTransaction();
        try {
            $promotion = Promotion::create([
                'batch_no' => $batchNo,
                'promoted_by' => auth()->id(),
                'students_count' => $studentsToPromote->count(),
                'filter_criteria' => $promoteAllFiltered ? $req->only([
                    'branch_id', 'semester_id', 'admission_session_id', 'is_profile', 'fee_status',
                    'student_name', 'registration_no', 'university_roll_no', 'course_id', 'registration_type'
                ]) : ['mode' => 'selected_ids', 'count' => $studentsToPromote->count()],
                'status' => 'promoted',
                'notes' => $req->input('notes', 'Promoted via admin panel'),
            ]);

            $grouped = $studentsToPromote->groupBy('semester_id');
            $fromSemNames = [];
            $toSemNames = [];
            $promotionRows = [];
            $totalUpdated = 0;

            foreach ($grouped as $fromSemId => $studentGroup) {
                $fromSemId = (int)$fromSemId;
                $toSemId = ($fromSemId < $maxSemId) ? ($fromSemId + 1) : $maxSemId;

                $fromName = $allSemesters[$fromSemId]->semester_name ?? ("Sem " . $fromSemId);
                $toName = $allSemesters[$toSemId]->semester_name ?? ("Sem " . $toSemId);
                $fromSemNames[] = $fromName;
                $toSemNames[] = $toName;

                $groupStudentIds = $studentGroup->pluck('id')->toArray();

                foreach ($groupStudentIds as $stId) {
                    $promotionRows[] = [
                        'promotion_id' => $promotion->id,
                        'student_id' => $stId,
                        'from_semester_id' => $fromSemId,
                        'to_semester_id' => $toSemId,
                        'status' => 'promoted',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Chunked update of students to handle lakhs cleanly
                foreach (array_chunk($groupStudentIds, 1000) as $chunkIds) {
                    Student::whereIn('id', $chunkIds)->update(['semester_id' => $toSemId]);
                }
                $totalUpdated += count($groupStudentIds);
            }

            // Chunked insert of audit history
            foreach (array_chunk($promotionRows, 1000) as $chunkRows) {
                PromotionStudent::insert($chunkRows);
            }

            $promotion->update([
                'from_semester_name' => implode(', ', array_unique($fromSemNames)),
                'to_semester_name' => implode(', ', array_unique($toSemNames)),
                'students_count' => $totalUpdated,
            ]);

            DB::commit();

            return redirect()->back()->with('success', "Successfully promoted {$totalUpdated} students (Batch #{$batchNo}). Disaster recovery backup created.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Promotion failed: ' . $e->getMessage());
        }
    }

    public function revertPromotion(Request $request, $id)
    {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);
        @ini_set('memory_limit', '1024M');

        $promotion = Promotion::with('promotionStudents')->findOrFail($id);

        if ($promotion->status === 'reverted') {
            return redirect()->back()->with('error', "Batch #{$promotion->batch_no} was already reverted on {$promotion->reverted_at->format('d M Y, h:i A')}.");
        }

        DB::beginTransaction();
        try {
            $groupedByFromSem = $promotion->promotionStudents
                ->where('status', 'promoted')
                ->groupBy('from_semester_id');

            $totalReverted = 0;
            foreach ($groupedByFromSem as $fromSemId => $records) {
                $studentIds = $records->pluck('student_id')->toArray();
                foreach (array_chunk($studentIds, 1000) as $chunkIds) {
                    Student::whereIn('id', $chunkIds)->update(['semester_id' => $fromSemId]);
                }
                $totalReverted += count($studentIds);
            }

            $promotion->promotionStudents()->where('status', 'promoted')->update([
                'status' => 'reverted',
                'reverted_at' => now(),
            ]);

            $promotion->update([
                'status' => 'reverted',
                'reverted_at' => now(),
                'reverted_by' => auth()->id(),
                'notes' => ($promotion->notes ? ($promotion->notes . ' | ') : '') . 'Reverted on ' . now()->toDateTimeString() . ' by ' . (auth()->user()->name ?? 'Admin'),
            ]);

            DB::commit();

            return redirect()->back()->with('success', "Disaster Recovery Successful: Batch #{$promotion->batch_no} reverted. {$totalReverted} students restored to their previous semester.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Reversion failed: ' . $e->getMessage());
        }
    }

    public function promotionHistory(Request $request)
    {
        $promotions = Promotion::with(['user', 'revertedByUser'])
            ->withCount('promotionStudents')
            ->latest()
            ->paginate(15);

        return view('admin.student.promotion-history', compact('promotions'));
    }

    public function promotionDetails($id)
    {
        $promotion = Promotion::with(['user', 'revertedByUser'])->findOrFail($id);
        $promotionStudents = PromotionStudent::with(['student.branch', 'student.admission_session', 'fromSemester', 'toSemester'])
            ->where('promotion_id', $id)
            ->paginate(50);

        return view('admin.student.promotion-details', compact('promotion', 'promotionStudents'));
    }
    public function update_roll_no(Request $req)
    {
        $req->validate([
            'id' => 'required|exists:students,id',
            'university_roll_no' => 'required|unique:students,university_roll_no,' . $req->id . ',id',
        ]);
        $student = Student::findOrFail($req->id);
        if ($student->update(['university_roll_no' => $req->university_roll_no])) {
            return response()->json(['message' => 'Updated successfully']);
        }
        return response()->json(['message' => 'Error'], 500);
    }

    public function getReciept($student_id)
    {
        $student = Student::with([
            'course',
            'branch',
            'admission_session',
            'profile_pic',
            'sign',
            'examForms' => function ($query) {
                $query->latest();
            },
            'examForms.subjects'
        ])->find($student_id);

        if (!$student) {
            return redirect()->back()->with('error', 'Student record not found.');
        }

        // Get the latest exam form to display subjects
        $examForm = $student->examForms->first();

        return view('admin.student.receipt', compact('student', 'examForm'));
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $courses = Course::with('branches')->get();
        $semesters = Semester::get();
        $admission_sessions = AdmissionSession::get();

        // Pass current course's branches for initial load
        $current_branches = Branch::where('course_id', $student->course_id)->get();

        return view('admin.student.edit', compact('student', 'courses', 'semesters', 'admission_sessions', 'current_branches'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
            'registration_no' => 'required|unique:students,registration_no,' . $id,
            'university_roll_no' => 'nullable|unique:students,university_roll_no,' . $id,
            'branch_id' => 'required|exists:branches,id',
            'semester_id' => 'required|exists:semesters,id',
            'admission_semester_id' => 'required|exists:semesters,id',
            'admission_session_id' => 'required|exists:admission_sessions,id',
        ]);

        $student = Student::findOrFail($id);

        // Update course_id based on selected branch
        $branch = Branch::findOrFail($request->branch_id);
        $course_id = $branch->course_id;

        $student->update([
            'student_name' => $request->student_name,
            'registration_no' => $request->registration_no,
            'university_roll_no' => $request->university_roll_no,
            'branch_id' => $request->branch_id,
            'course_id' => $course_id,
            'semester_id' => $request->semester_id,
            'admission_semester_id' => $request->admission_semester_id,
            'admission_session_id' => $request->admission_session_id,
        ]);

        return redirect()->route('admin.student.list')->with('success', 'Student details updated successfully.');
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $student = Student::findOrFail($id);
        $student->update([
            'password' => bcrypt($request->password),
        ]);

        return redirect()->back()->with('success', 'Password reset successfully.');
    }
    public function create()
    {
        $courses = Course::with('branches')->get();
        $semesters = Semester::get();
        $admission_sessions = AdmissionSession::get();
        return view('admin.student.create', compact('courses', 'semesters', 'admission_sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
            'registration_no' => 'nullable|unique:students,registration_no',
            'university_roll_no' => 'nullable|unique:students,university_roll_no',
            'password' => 'required|min:6',
            'branch_id' => 'required|exists:branches,id',
            'semester_id' => 'required|exists:semesters,id',
            'admission_semester_id' => 'required|exists:semesters,id',
            'admission_session_id' => 'required|exists:admission_sessions,id',
            'registration_type' => 'required|in:normal,lateral',
        ]);

        // Auto-generate Registration No if empty
        $registration_no = $request->registration_no;
        if (empty($registration_no)) {
            $registration_no = $this->generateRegistrationNo($request->admission_session_id);
        }

        // Auto-generate University Roll No if empty
        $university_roll_no = $request->university_roll_no;
        if (empty($university_roll_no)) {
            $university_roll_no = $this->generateUniversityRollNo($request->branch_id, $request->admission_session_id);
        }

        $branch = Branch::findOrFail($request->branch_id);
        $course_id = $branch->course_id;

        Student::create([
            'student_name' => $request->student_name,
            'registration_no' => $registration_no,
            'university_roll_no' => $university_roll_no,
            'password' => bcrypt($request->password),
            'branch_id' => $request->branch_id,
            'course_id' => $course_id,
            'semester_id' => $request->semester_id,
            'admission_semester_id' => $request->admission_semester_id,
            'admission_session_id' => $request->admission_session_id,
            'registration_type' => $request->registration_type,
        ]);

        return redirect()->route('admin.student.list')->with('success', 'Student created successfully.');
    }
}
