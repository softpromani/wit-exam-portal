<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionSession;
use App\Models\Branch;
use App\Models\Course;
use App\Models\ImportFail;
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
        $res=$this->processExcel(storage_path("app/$filePath"), $fileName, $request, $course,$branch);
        return back()->with('success', 'File uploaded and import process started. '.$res['success'].'imported successfully and '.$res['fail'].' failed');
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
        $success_count=0;
        $fail_count=0;
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
                    'university_roll_no'=>$this->generateUniversityRollNo($request->branch_id,$request->admission_session_id),
                    'registration_no'=> $this->generateRegistrationNo($request->admission_session_id),
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
                    'reason'=>$e->getMessage()
                ]);
                $fail_count++;
            }
        }
        return ['success'=>$success_count,'fail'=>$fail_count];
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
    public function getStudents(Request $req){
        if($req->ajax()){
            $query = Student::query();

        // Apply search filter
        if ($req->has('search') && !empty($req->search)) {
            $search = $req->search;
            $query->where(function ($q) use ($search) {
                $q->where('university_roll_no', 'like', "%$search%")
                  ->orWhere('registration_no', 'like', "%$search%")
                  ->orWhere('registration_type', 'like', "%$search%")
                  ->orWhere('student_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('mobile_number', 'like', "%$search%");
            });
        }

        // Apply sorting
        if ($req->has('sortField') && $req->has('sortOrder')) {
            $query->orderBy($req->sortField, $req->sortOrder);
        } else {
            $query->orderBy('id', 'desc'); // Default sorting
        }

        // Apply pagination
        $students = $query->paginate($req->limit ?? 10);
         // Define columns
         $columns = [
            ["title" => "University Roll No", "field" => "university_roll_no", "sorter" => "string", "headerFilter" => "input"],
            ["title" => "Registration No", "field" => "registration_no", "sorter" => "string", "headerFilter" => "input"],
            ["title" => "Student Name", "field" => "student_name", "sorter" => "string", "headerFilter" => "input"],
            ["title" => "Email", "field" => "email", "sorter" => "string", "headerFilter" => "input"],
            ["title" => "Mobile", "field" => "mobile_number", "sorter" => "string", "headerFilter" => "input"],
        ];
        return response()->json([
            'last_page' => $students->lastPage(),
            'data' => $students->items(),
            'total' => $students->total(),
            'columns' => $columns
        ]);
        }
        return view('admin.student.list');
    }
}
