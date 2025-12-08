<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionSession;
use App\Models\Branch;
use App\Models\Course;
use App\Models\ImportFail;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'excel' => 'required|file|mimes:xlsx,csv|max:2048'
        ]);

        // Fetch course_id from branches table
        $branch = Branch::findOrFail($request->branch_id);
        $course = $branch->course; // Get the associated course_id

        // Generate a unique filename
        $extension = $request->file('excel')->getClientOriginalExtension();
        $fileName = $this->generateUniqueFileName($request->registration_type, $request->branch_id, $request->admission_session_id, $extension);

        // Store the file
        $filePath = $request->file('excel')->storeAs('uploads/students', $fileName);

        // Process Excel File with additional course_id parameter
        $res = $this->processExcel(storage_path("app/$filePath"), $fileName, $request, $course, $branch);
        return back()->with('success', 'File uploaded and import process started. ' . $res['success'] . 'imported successfully and ' . $res['fail'] . ' failed');
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
        $success_count = 0;
        $fail_count = 0;
        // Load Excel File
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (empty($rows) || count($rows) < 2) {
            return; // No data to process
        }

        // Extract headers from the first row
        $headers = array_map('strtolower', $rows[0]); // Convert to lowercase for case-insensitivity

        // Define the required fields and find their index positions
        $nameIndex = array_search('name', $headers);
        $passwordIndex = array_search('password', $headers);
        $semesterIndex = array_search('semester', $headers);

        // Validate required columns exist in the header
        if ($nameIndex === false || $passwordIndex === false || $semesterIndex === false) {
            ImportFail::create([
                'file_name' => $fileName,
                'row_data' => json_encode(['error' => 'Missing required columns: name, password, semester']),
            ]);
            return;
        }

        // Process each row starting from the second row (skip headers)
        foreach (array_slice($rows, 1) as $row) {
            try {
                // Insert into Students table
                Student::create([
                    'university_roll_no' => $this->generateUniversityRollNo($request->branch_id, $request->admission_session_id),
                    'registration_no' => $this->generateRegistrationNo($request->admission_session_id),
                    'student_name' => $row[$nameIndex] ?? null,
                    'semester_id' => $row[$semesterIndex] ?? null,
                    'password' => bcrypt($row[$passwordIndex] ?? null), // Hash password
                    'branch_id' => $request->branch_id,
                    'course_id' => $course->id, // Assuming course_id is same as branch_id
                    'admission_session_id' => $request->admission_session_id,
                    'registration_type' => $request->registration_type,
                ]);
                $success_count++;
            } catch (\Exception $e) {
                // Store failed row in ImportFail table
                ImportFail::create([
                    'file_name' => $fileName,
                    'row_data' => json_encode($row),
                    'reason' => $e->getMessage()
                ]);
                $fail_count++;
            }
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


    // get all student list
    public function getStudents(Request $request)
    {
        $arrView = [
            'courses' => Course::with('branches')->get(),
            'semesters' => Semester::get(),
            'admission_sessions' => AdmissionSession::get(),
        ];
        if ($request->post()) {
            $query = Student::query()->with(['semester', 'branch', 'admission_session', 'admission_semester']);

            // Apply search filters for each column
            if ($request->has('university_roll_no')) {
                $query->where('university_roll_no', 'like', '%' . $request->university_roll_no . '%');
            }
            if ($request->has('registration_no')) {
                $query->where('registration_no', 'like', '%' . $request->registration_no . '%');
            }
            if ($request->has('registration_type')) {
                $query->where('registration_type', 'like', '%' . $request->registration_type . '%');
            }
            if ($request->has('student_name')) {
                $query->where('student_name', 'like', '%' . $request->student_name . '%');
            }
            if ($request->has('course_id')) {
                $query->where('course_id', $request->course_id);
            }
            if ($request->has('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }
            if ($request->has('semester_id')) {
                $query->where('semester_id', $request->semester_id);
            }
            if ($request->has('admission_session_id')) {
                $query->where('admission_session_id', $request->admission_session_id);
            }

            $arrView['students'] = $query->get();
        }
        return view('admin.student.list', $arrView);
    }



    public function promote(Request $req)
    {
        $validatedData = $req->validate([
            'selected_students' => 'required|array', // Ensure at least one student is selected
            'selected_students.*' => 'exists:students,id', // Validate each student ID exists in the database
        ]);
        $studentIds = $req->input('selected_students');
        $res = Student::whereIn('id', $studentIds)->increment('semester_id');
        return redirect()->back()->with('success', 'Students Promoted');
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
}
