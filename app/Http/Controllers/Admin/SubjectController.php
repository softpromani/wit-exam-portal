<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Course;
use App\Models\ExamFormSubject;
use App\Models\ExamSchedule;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects with filters and search.
     */
    public function index(Request $request)
    {
        $query = Subject::with(['course', 'branch', 'semester']);

        // Filter by Course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by Branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by Semester
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // Filter by Type
        if ($request->filled('type')) {
            $type = $request->type;
            if ($type === 't') {
                $query->where(function ($q) {
                    $q->where('type', 't')->orWhere('type', 'Theory');
                });
            } elseif ($type === 'p') {
                $query->where(function ($q) {
                    $q->where('type', 'p')->orWhere('type', 'Practical');
                });
            } else {
                $query->where('type', $type);
            }
        }

        // Search by Subject Code or Title
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('subject_code', 'LIKE', "%{$search}%")
                  ->orWhere('title', 'LIKE', "%{$search}%");
            });
        }

        $totalSubjects = Subject::count();
        $filteredCount = (clone $query)->count();
        $subjects = $query->orderBy('course_id')
                          ->orderBy('branch_id')
                          ->orderBy('semester_id')
                          ->orderBy('subject_code')
                          ->paginate(20)
                          ->withQueryString();

        $courses = Course::with('branches')->get();
        $branches = Branch::all();
        $semesters = Semester::all();

        return view('admin.subject.index', compact(
            'subjects',
            'courses',
            'branches',
            'semesters',
            'totalSubjects',
            'filteredCount'
        ));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        $courses = Course::with('branches')->get();
        $branches = Branch::all();
        $semesters = Semester::all();

        return view('admin.subject.create', compact('courses', 'branches', 'semesters'));
    }

    /**
     * Store a newly created subject in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_code' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'branch_id' => 'required|exists:branches,id',
            'semester_id' => 'required|exists:semesters,id',
            'type' => 'nullable|string|max:50',
        ]);

        Subject::create($validated);

        return redirect()->route('admin.subject.index')
                         ->with('success', 'Subject created successfully!');
    }

    /**
     * Show the form for editing the specified subject.
     */
    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        $courses = Course::with('branches')->get();
        $branches = Branch::all();
        $semesters = Semester::all();

        return view('admin.subject.edit', compact('subject', 'courses', 'branches', 'semesters'));
    }

    /**
     * Update the specified subject in storage.
     */
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'subject_code' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'branch_id' => 'required|exists:branches,id',
            'semester_id' => 'required|exists:semesters,id',
            'type' => 'nullable|string|max:50',
        ]);

        $subject->update($validated);

        return redirect()->route('admin.subject.index')
                         ->with('success', 'Subject updated successfully!');
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);

        // Check if subject is associated with exam schedules
        $hasSchedules = ExamSchedule::where('subject_id', $id)->exists();
        if ($hasSchedules) {
            return redirect()->back()->with('error', 'Cannot delete this subject because it is linked to active Exam Schedules.');
        }

        // Check if subject is associated with student exam forms
        $hasExamForms = ExamFormSubject::where('subject_id', $id)->exists();
        if ($hasExamForms) {
            return redirect()->back()->with('error', 'Cannot delete this subject because it is associated with Student Exam Forms.');
        }

        $subject->delete();

        return redirect()->route('admin.subject.index')
                         ->with('success', 'Subject deleted successfully!');
    }

    /**
     * Show the CSV / Excel import form.
     */
    public function importView()
    {
        $courses = Course::with('branches')->get();
        $branches = Branch::all();
        $semesters = Semester::all();

        return view('admin.subject.import', compact('courses', 'branches', 'semesters'));
    }

    /**
     * Download a sample CSV file template.
     */
    public function downloadSampleCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="subjects_template.csv"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // Header columns
            fputcsv($handle, ['subject_code', 'title', 'course', 'branch', 'semester', 'type']);
            // Sample rows matching system data
            fputcsv($handle, ['HS-1102', 'Organizational Behaviour and Industrial Psychology', 'B.tech', 'IT', 'sem-1', 'Theory']);
            fputcsv($handle, ['MA-1101', 'Mathematics-1', 'B.tech', 'IT', 'sem-1', 'Theory']);
            fputcsv($handle, ['CS-1301', 'Object Oriented Programming', 'B.tech', 'CSE', 'sem-3', 'Theory']);
            fputcsv($handle, ['CS-1302', 'Object Oriented Programming Lab', 'B.tech', 'CSE', 'sem-3', 'Practical']);
            fputcsv($handle, ['BCA-101', 'Computer Fundamentals', 'BCA', 'BCA', 'sem-1', 'Theory']);
            fputcsv($handle, ['MCA-101', 'Advanced Data Structures', 'MCA', 'MCA', 'sem-1', 'Theory']);
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Process CSV or Excel file import for subjects.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:51200',
            'duplicate_action' => 'required|in:skip,update',
            'default_course_id' => 'nullable|exists:courses,id',
            'default_branch_id' => 'nullable|exists:branches,id',
            'default_semester_id' => 'nullable|exists:semesters,id',
        ]);

        @ini_set('max_execution_time', 300);
        @set_time_limit(300);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        // Load lookup maps into memory for ultra-fast matching
        $allCourses = Course::all();
        $allBranches = Branch::all();
        $allSemesters = Semester::all();

        $courseById = $allCourses->keyBy('id');
        $courseByName = [];
        foreach ($allCourses as $c) {
            $courseByName[strtolower(trim($c->name))] = $c->id;
            if ($c->full_name) {
                $courseByName[strtolower(trim($c->full_name))] = $c->id;
            }
        }

        $branchById = $allBranches->keyBy('id');
        $branchByName = [];
        foreach ($allBranches as $b) {
            $branchByName[strtolower(trim($b->name))] = $b->id;
            if ($b->full_name) {
                $branchByName[strtolower(trim($b->full_name))] = $b->id;
            }
        }

        $semesterById = $allSemesters->keyBy('id');
        $semesterByName = [];
        foreach ($allSemesters as $s) {
            $semKey = strtolower(trim($s->semester_name));
            $semesterByName[$semKey] = $s->id;
            // Also map "sem 1", "sem-1", "1", "semester 1"
            $numOnly = preg_replace('/[^0-9]/', '', $semKey);
            if ($numOnly !== '') {
                $semesterByName[$numOnly] = $s->id;
                $semesterByName["sem{$numOnly}"] = $s->id;
                $semesterByName["sem-{$numOnly}"] = $s->id;
                $semesterByName["semester {$numOnly}"] = $s->id;
                $semesterByName["semester-{$numOnly}"] = $s->id;
            }
        }

        // Read all rows into an array
        $rows = [];
        if ($ext === 'csv' || $ext === 'txt') {
            if (($handle = fopen($path, 'r')) !== false) {
                while (($data = fgetcsv($handle)) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        } else {
            // Excel (.xlsx, .xls)
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        }

        if (count($rows) < 2) {
            return redirect()->back()->with('error', 'The uploaded file is empty or missing data rows.');
        }

        // Determine header indices
        $headerRow = array_shift($rows);
        $headerMap = [];
        foreach ($headerRow as $idx => $headerName) {
            $cleanName = strtolower(trim((string)$headerName));
            $cleanName = str_replace(['_', '-'], ' ', $cleanName);
            $headerMap[$cleanName] = $idx;
        }

        // Find relevant column indexes with flexible synonyms
        $codeIdx = $this->findHeaderIndex($headerMap, ['subject code', 'code', 'subjectcode', 'subject_code']);
        $titleIdx = $this->findHeaderIndex($headerMap, ['title', 'subject title', 'subject name', 'name', 'subject']);
        $courseIdx = $this->findHeaderIndex($headerMap, ['course', 'course id', 'course name']);
        $branchIdx = $this->findHeaderIndex($headerMap, ['branch', 'branch id', 'branch name']);
        $semesterIdx = $this->findHeaderIndex($headerMap, ['semester', 'semester id', 'sem']);
        $typeIdx = $this->findHeaderIndex($headerMap, ['type', 'subject type']);

        if ($codeIdx === null || $titleIdx === null) {
            return redirect()->back()->with('error', 'File must contain "subject_code" and "title" columns.');
        }

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $rowNum => $row) {
            $actualLine = $rowNum + 2; // account for header and 1-based indexing

            // Skip empty rows
            if (empty(array_filter($row, fn($val) => trim((string)$val) !== ''))) {
                continue;
            }

            $subjectCode = trim((string)($row[$codeIdx] ?? ''));
            $title = trim((string)($row[$titleIdx] ?? ''));

            if ($subjectCode === '' || $title === '') {
                $skipped++;
                $errors[] = "Row {$actualLine}: Subject code or title was empty.";
                continue;
            }

            // Resolve Course
            $rawCourse = $courseIdx !== null ? trim((string)($row[$courseIdx] ?? '')) : '';
            $courseId = null;
            if ($rawCourse !== '') {
                if (is_numeric($rawCourse) && isset($courseById[(int)$rawCourse])) {
                    $courseId = (int)$rawCourse;
                } else {
                    $cKey = strtolower($rawCourse);
                    $courseId = $courseByName[$cKey] ?? null;
                }
            }
            if (!$courseId) {
                $courseId = $request->default_course_id;
            }
            if (!$courseId) {
                $skipped++;
                $errors[] = "Row {$actualLine} ({$subjectCode}): Invalid or missing Course.";
                continue;
            }

            // Resolve Branch
            $rawBranch = $branchIdx !== null ? trim((string)($row[$branchIdx] ?? '')) : '';
            $branchId = null;
            if ($rawBranch !== '') {
                if (is_numeric($rawBranch) && isset($branchById[(int)$rawBranch])) {
                    $branchId = (int)$rawBranch;
                } else {
                    $bKey = strtolower($rawBranch);
                    $branchId = $branchByName[$bKey] ?? null;
                }
            }
            if (!$branchId) {
                $branchId = $request->default_branch_id;
            }
            if (!$branchId) {
                $skipped++;
                $errors[] = "Row {$actualLine} ({$subjectCode}): Invalid or missing Branch.";
                continue;
            }

            // Resolve Semester
            $rawSemester = $semesterIdx !== null ? trim((string)($row[$semesterIdx] ?? '')) : '';
            $semesterId = null;
            if ($rawSemester !== '') {
                if (is_numeric($rawSemester) && isset($semesterById[(int)$rawSemester])) {
                    $semesterId = (int)$rawSemester;
                } else {
                    $sKey = strtolower($rawSemester);
                    $semesterId = $semesterByName[$sKey] ?? null;
                }
            }
            if (!$semesterId) {
                $semesterId = $request->default_semester_id;
            }
            if (!$semesterId) {
                $skipped++;
                $errors[] = "Row {$actualLine} ({$subjectCode}): Invalid or missing Semester.";
                continue;
            }

            // Resolve Type (t / p / etc.)
            $rawType = $typeIdx !== null ? strtolower(trim((string)($row[$typeIdx] ?? ''))) : '';
            $type = 't'; // default to theory
            if ($rawType === 'p' || $rawType === 'practical' || str_contains($rawType, 'lab') || str_contains($rawType, 'practical')) {
                $type = 'p';
            } elseif ($rawType === 't' || $rawType === 'theory' || str_contains($rawType, 'theory')) {
                $type = 't';
            } elseif ($rawType !== '') {
                $type = $rawType;
            }

            // Check duplicate by subject_code, course_id, branch_id, semester_id
            $existing = Subject::where('subject_code', $subjectCode)
                ->where('course_id', $courseId)
                ->where('branch_id', $branchId)
                ->where('semester_id', $semesterId)
                ->first();

            if ($existing) {
                if ($request->duplicate_action === 'update') {
                    $existing->update([
                        'title' => $title,
                        'type' => $type,
                    ]);
                    $updated++;
                } else {
                    $skipped++;
                }
            } else {
                Subject::create([
                    'subject_code' => $subjectCode,
                    'title' => $title,
                    'course_id' => $courseId,
                    'branch_id' => $branchId,
                    'semester_id' => $semesterId,
                    'type' => $type,
                ]);
                $imported++;
            }
        }

        $message = "Import completed successfully! Imported: {$imported}, Updated: {$updated}, Skipped: {$skipped}.";

        if (!empty($errors)) {
            // Keep first 5 errors to avoid huge session payloads
            $previewErrors = array_slice($errors, 0, 5);
            $msg = $message . " Note: " . implode(' | ', $previewErrors);
            if (count($errors) > 5) {
                $msg .= " (and " . (count($errors) - 5) . " more)";
            }
            return redirect()->route('admin.subject.index')->with('success', $msg);
        }

        return redirect()->route('admin.subject.index')->with('success', $message);
    }

    /**
     * Helper to find index from header array matching candidate names.
     */
    private function findHeaderIndex(array $headerMap, array $candidates): ?int
    {
        foreach ($candidates as $candidate) {
            if (isset($headerMap[$candidate])) {
                return $headerMap[$candidate];
            }
        }
        return null;
    }
}
