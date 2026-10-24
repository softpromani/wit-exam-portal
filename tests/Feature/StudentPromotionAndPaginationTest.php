<?php

namespace Tests\Feature;

use App\Models\AdmissionSession;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Promotion;
use App\Models\PromotionStudent;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class StudentPromotionAndPaginationTest extends TestCase
{
    protected User $admin;
    protected Branch $branch;
    protected AdmissionSession $session;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::first() ?? User::factory()->create([
            'email' => 'admin@witlnmu.ac.in',
        ]);

        $this->branch = Branch::first() ?? Branch::create([
            'name' => 'Computer Science & Engineering',
            'course_id' => Course::first()?->id ?? 1,
        ]);

        $this->session = AdmissionSession::first() ?? AdmissionSession::create([
            'session_name' => '2024-2028',
            'from' => '2024-01-01',
            'to' => '2028-12-31',
            'status' => 'active',
        ]);
    }

    public function test_admin_student_list_is_paginated(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.student.list', [
            'fetch' => 1,
            'per_page' => 10,
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('students');
        $students = $response->viewData('students');

        $this->assertInstanceOf(LengthAwarePaginator::class, $students);
        $this->assertEquals(10, $students->perPage());
    }

    public function test_admin_can_promote_selected_students_and_creates_audit_record(): void
    {
        // Create 2 test students in semester 1
        $uniqueSuffix = rand(10000, 99999);
        $s1 = Student::create([
            'student_name' => 'Test Promo Student 1 ' . $uniqueSuffix,
            'registration_no' => (int)('24' . rand(100000, 999999)),
            'university_roll_no' => 'TEST-24-' . rand(1000, 9999),
            'branch_id' => $this->branch->id,
            'course_id' => $this->branch->course_id ?? 1,
            'admission_session_id' => $this->session->id,
            'semester_id' => 1,
            'admission_semester_id' => 1,
            'registration_type' => 'normal',
            'password' => bcrypt('password'),
        ]);

        $s2 = Student::create([
            'student_name' => 'Test Promo Student 2 ' . $uniqueSuffix,
            'registration_no' => (int)('24' . rand(100000, 999999)),
            'university_roll_no' => 'TEST-24-' . rand(1000, 9999),
            'branch_id' => $this->branch->id,
            'course_id' => $this->branch->course_id ?? 1,
            'admission_session_id' => $this->session->id,
            'semester_id' => 1,
            'admission_semester_id' => 1,
            'registration_type' => 'normal',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.student-promote'), [
            'selected_students' => [$s1->id, $s2->id],
            'notes' => 'Test Promotion Batch',
        ]);

        $response->assertStatus(302);
        $this->assertTrue(in_array('success', $response->getSession()->get('_flash')['old'] ?? []) || $response->getSession()->has('success'));

        // Check students semester incremented to semester 2
        $s1->refresh();
        $s2->refresh();
        $this->assertEquals(2, $s1->semester_id);
        $this->assertEquals(2, $s2->semester_id);

        // Check audit record created in promotions table
        $promotion = Promotion::latest('id')->first();
        $this->assertNotNull($promotion);
        $this->assertEquals('promoted', $promotion->status);
        $this->assertEquals(2, $promotion->students_count);

        // Check promotion_students audit entries
        $records = PromotionStudent::where('promotion_id', $promotion->id)->get();
        $this->assertCount(2, $records);
        foreach ($records as $rec) {
            $this->assertEquals(1, $rec->from_semester_id);
            $this->assertEquals(2, $rec->to_semester_id);
            $this->assertEquals('promoted', $rec->status);
        }
    }

    public function test_admin_can_promote_all_filtered_students(): void
    {
        $uniqueSuffix = rand(10000, 99999);
        $namePrefix = 'FilteredCohort_' . $uniqueSuffix;

        $st1 = Student::create([
            'student_name' => $namePrefix . '_A',
            'registration_no' => (int)('25' . rand(100000, 999999)),
            'university_roll_no' => 'TEST-25-' . rand(1000, 9999),
            'branch_id' => $this->branch->id,
            'course_id' => $this->branch->course_id ?? 1,
            'admission_session_id' => $this->session->id,
            'semester_id' => 3,
            'admission_semester_id' => 1,
            'registration_type' => 'lateral',
            'password' => bcrypt('password'),
        ]);

        $st2 = Student::create([
            'student_name' => $namePrefix . '_B',
            'registration_no' => (int)('25' . rand(100000, 999999)),
            'university_roll_no' => 'TEST-25-' . rand(1000, 9999),
            'branch_id' => $this->branch->id,
            'course_id' => $this->branch->course_id ?? 1,
            'admission_session_id' => $this->session->id,
            'semester_id' => 3,
            'admission_semester_id' => 1,
            'registration_type' => 'lateral',
            'password' => bcrypt('password'),
        ]);

        // Promote all matching this filter
        $response = $this->actingAs($this->admin)->post(route('admin.student-promote'), [
            'promote_all_filtered' => '1',
            'branch_id' => $this->branch->id,
            'semester_id' => 3,
            'admission_session_id' => $this->session->id,
            'registration_type' => 'lateral',
            'student_name' => $namePrefix,
        ]);

        $response->assertStatus(302);
        $this->assertTrue(in_array('success', $response->getSession()->get('_flash')['old'] ?? []) || $response->getSession()->has('success'));

        $st1->refresh();
        $st2->refresh();
        $this->assertEquals(4, $st1->semester_id);
        $this->assertEquals(4, $st2->semester_id);
    }

    public function test_admin_can_revert_promotion_disaster_recovery(): void
    {
        $uniqueSuffix = rand(10000, 99999);
        $student = Student::create([
            'student_name' => 'Disaster Recovery Student ' . $uniqueSuffix,
            'registration_no' => (int)('26' . rand(100000, 999999)),
            'university_roll_no' => 'TEST-26-' . rand(1000, 9999),
            'branch_id' => $this->branch->id,
            'course_id' => $this->branch->course_id ?? 1,
            'admission_session_id' => $this->session->id,
            'semester_id' => 2,
            'admission_semester_id' => 1,
            'registration_type' => 'normal',
            'password' => bcrypt('password'),
        ]);

        // 1. Promote student from sem 2 to sem 3
        $this->actingAs($this->admin)->post(route('admin.student-promote'), [
            'selected_students' => [$student->id],
            'notes' => 'Promotion to be reverted',
        ]);

        $student->refresh();
        $this->assertEquals(3, $student->semester_id);

        $promotion = Promotion::latest('id')->first();
        $this->assertEquals('promoted', $promotion->status);

        // 2. Perform Disaster Recovery Revert
        $revertResponse = $this->actingAs($this->admin)->post(route('admin.student.promotion-revert', $promotion->id));
        $revertResponse->assertStatus(302);
        $this->assertTrue(in_array('success', $revertResponse->getSession()->get('_flash')['old'] ?? []) || $revertResponse->getSession()->has('success'));

        // Check student has been rolled back to semester 2
        $student->refresh();
        $this->assertEquals(2, $student->semester_id);

        // Check promotion record marked as reverted
        $promotion->refresh();
        $this->assertEquals('reverted', $promotion->status);
        $this->assertNotNull($promotion->reverted_at);

        // Check individual promotion_student marked as reverted
        $pStudent = PromotionStudent::where('promotion_id', $promotion->id)->first();
        $this->assertEquals('reverted', $pStudent->status);
        $this->assertNotNull($pStudent->reverted_at);

        // 3. Attempting to revert again fails with error
        $secondRevert = $this->actingAs($this->admin)->post(route('admin.student.promotion-revert', $promotion->id));
        $secondRevert->assertStatus(302);
        $this->assertTrue(in_array('error', $secondRevert->getSession()->get('_flash')['old'] ?? []) || $secondRevert->getSession()->has('error'));
    }

    public function test_admin_promotion_history_and_details_views(): void
    {
        $historyResponse = $this->actingAs($this->admin)->get(route('admin.student.promotion-history'));
        $historyResponse->assertSee('Student Promotion History');
        $historyResponse->assertSee('Disaster Recovery');

        $promotion = Promotion::latest('id')->first();
        if ($promotion) {
            $detailsResponse = $this->actingAs($this->admin)->get(route('admin.student.promotion-details', $promotion->id));
            $detailsResponse->assertStatus(200);
            $detailsResponse->assertSee($promotion->batch_no);
        }
    }

    public function test_admin_can_import_csv_in_batch_with_roll_and_reg_generation(): void
    {
        $csvContent = "name,password,semester\n" .
            "Imported Student Alpha,pass123,1\n" .
            "Imported Student Beta,pass123,1\n" .
            "Imported Student Gamma,pass123,2\n";

        $tmpFile = tempnam(sys_get_temp_dir(), 'test_import_') . '.csv';
        file_put_contents($tmpFile, $csvContent);

        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $tmpFile,
            'test_students.csv',
            'text/csv',
            null,
            true
        );

        $response = $this->actingAs($this->admin)->post(route('admin.student.import'), [
            'registration_type' => 'normal',
            'branch_id' => $this->branch->id,
            'admission_session_id' => $this->session->id,
            'excel' => $uploadedFile,
        ]);

        $response->assertStatus(302);
        $this->assertTrue(in_array('success', $response->getSession()->get('_flash')['old'] ?? []) || $response->getSession()->has('success'));

        $this->assertDatabaseHas('students', [
            'student_name' => 'Imported Student Alpha',
            'branch_id' => $this->branch->id,
            'semester_id' => 1,
        ]);
        $this->assertDatabaseHas('students', [
            'student_name' => 'Imported Student Beta',
            'branch_id' => $this->branch->id,
            'semester_id' => 1,
        ]);
        $this->assertDatabaseHas('students', [
            'student_name' => 'Imported Student Gamma',
            'branch_id' => $this->branch->id,
            'semester_id' => 2,
        ]);

        if (file_exists($tmpFile)) {
            @unlink($tmpFile);
        }
    }
}
