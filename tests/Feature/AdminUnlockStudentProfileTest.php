<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use App\Models\Branch;
use App\Models\Semester;
use App\Models\AdmissionSession;
use Tests\TestCase;

class AdminUnlockStudentProfileTest extends TestCase
{
    protected User $admin;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::first() ?? User::factory()->create([
            'email' => 'admin@witlnmu.ac.in',
        ]);

        $this->student = Student::first() ?? Student::create([
            'student_name' => 'Test Student',
            'registration_no' => 999999,
            'university_roll_no' => 'TEST-001',
            'course_id' => Course::first()?->id ?? 1,
            'branch_id' => Branch::first()?->id ?? 1,
            'semester_id' => Semester::first()?->id ?? 1,
            'admission_semester_id' => Semester::first()?->id ?? 1,
            'admission_session_id' => AdmissionSession::first()?->id ?? 1,
            'password' => bcrypt('password'),
            'is_profile' => 1,
        ]);
    }

    public function test_admin_can_unlock_student_profile(): void
    {
        $this->student->update(['is_profile' => 1]);

        $response = $this->actingAs($this->admin)
            ->from(route('admin.student.list'))
            ->post(route('admin.student.unlock-profile', $this->student->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.student.list'));

        $this->student->refresh();
        $this->assertEquals(0, $this->student->is_profile);
    }

    public function test_admin_can_lock_student_profile(): void
    {
        $this->student->update(['is_profile' => 0]);

        $response = $this->actingAs($this->admin)
            ->from(route('admin.student.list'))
            ->post(route('admin.student.lock-profile', $this->student->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.student.list'));

        $this->student->refresh();
        $this->assertEquals(1, $this->student->is_profile);
    }

    public function test_admin_can_unlock_profile_via_ajax(): void
    {
        $this->student->update(['is_profile' => 1]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.student.unlock-profile', $this->student->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_profile' => 0,
        ]);

        $this->student->refresh();
        $this->assertEquals(0, $this->student->is_profile);
    }

    public function test_admin_can_bulk_unlock_profiles(): void
    {
        $student2 = Student::skip(1)->first() ?? Student::create([
            'student_name' => 'Second Student',
            'registration_no' => 999998,
            'university_roll_no' => 'TEST-002',
            'course_id' => Course::first()?->id ?? 1,
            'branch_id' => Branch::first()?->id ?? 1,
            'semester_id' => Semester::first()?->id ?? 1,
            'admission_semester_id' => Semester::first()?->id ?? 1,
            'admission_session_id' => AdmissionSession::first()?->id ?? 1,
            'password' => bcrypt('password'),
            'is_profile' => 1,
        ]);

        $this->student->update(['is_profile' => 1]);
        $student2->update(['is_profile' => 1]);

        $response = $this->actingAs($this->admin)
            ->from(route('admin.student.list'))
            ->post(route('admin.student.bulk-unlock-profiles'), [
                'selected_students' => [$this->student->id, $student2->id],
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.student.list'));

        $this->student->refresh();
        $student2->refresh();

        $this->assertEquals(0, $this->student->is_profile);
        $this->assertEquals(0, $student2->is_profile);
    }

    public function test_admin_can_bulk_lock_profiles(): void
    {
        $student2 = Student::skip(1)->first() ?? $this->student;

        $this->student->update(['is_profile' => 0]);
        $student2->update(['is_profile' => 0]);

        $response = $this->actingAs($this->admin)
            ->from(route('admin.student.list'))
            ->post(route('admin.student.bulk-lock-profiles'), [
                'selected_students' => [$this->student->id, $student2->id],
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.student.list'));

        $this->student->refresh();
        $student2->refresh();

        $this->assertEquals(1, $this->student->is_profile);
        $this->assertEquals(1, $student2->is_profile);
    }

    public function test_unlocked_student_can_update_profile_and_locks_automatically(): void
    {
        $this->student->update(['is_profile' => 0]);

        $response = $this->actingAs($this->student, 'student')
            ->post(route('student.store'), [
                'student_name' => 'Updated Test Name',
                'gender' => 'male',
                'email' => 'updated_student@example.com',
                'mobile_number' => '9876543210',
                'fname' => 'Father Name',
                'mname' => 'Mother Name',
                'dob' => '2001-01-01',
                'adhar_number' => '123456789012',
                'parent_number' => '9876543211',
                'address' => 'Sample Address, City',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('student.dashboard'));

        $this->student->refresh();
        $this->assertEquals(1, $this->student->is_profile);
        $this->assertEquals('Updated Test Name', $this->student->student_name);
        $this->assertEquals('updated_student@example.com', $this->student->email);
    }

    public function test_locked_student_cannot_update_profile(): void
    {
        $this->student->update([
            'is_profile' => 1,
            'student_name' => 'Original Locked Name',
        ]);

        $response = $this->actingAs($this->student, 'student')
            ->post(route('student.store'), [
                'student_name' => 'Hacker Name',
                'gender' => 'male',
                'email' => 'hacker@example.com',
                'mobile_number' => '9876543210',
                'fname' => 'Father',
                'mname' => 'Mother',
                'dob' => '2001-01-01',
                'adhar_number' => '123456789012',
                'parent_number' => '9876543211',
                'address' => 'Sample Address',
            ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('student.dashboard'));

        $this->student->refresh();
        $this->assertEquals('Original Locked Name', $this->student->student_name);
        $this->assertEquals(1, $this->student->is_profile);
    }
}
