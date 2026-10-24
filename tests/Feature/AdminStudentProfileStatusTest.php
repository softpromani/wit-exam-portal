<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Tests\TestCase;

class AdminStudentProfileStatusTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::first() ?? User::factory()->create([
            'email' => 'admin@witlnmu.ac.in',
        ]);
    }

    public function test_admin_student_list_loads_with_global_profile_counts(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.student.list'));

        $response->assertStatus(200);
        $response->assertViewHas('globalTotalStudents');
        $response->assertViewHas('globalCompletedStudents');
        $response->assertViewHas('globalIncompleteStudents');
        $response->assertSee('Profile Completed');
        $response->assertSee('Profile Incomplete');
    }

    public function test_admin_can_filter_completed_profiles_via_get(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.student.list', ['is_profile' => '1']));

        $response->assertStatus(200);
        $response->assertViewHas('students');
        $students = $response->viewData('students');

        foreach ($students as $student) {
            $this->assertEquals(1, $student->is_profile);
        }
    }

    public function test_admin_can_filter_incomplete_profiles_via_get(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.student.list', ['is_profile' => '0']));

        $response->assertStatus(200);
        $response->assertViewHas('students');
        $students = $response->viewData('students');

        foreach ($students as $student) {
            $this->assertTrue(in_array($student->is_profile, [0, null, false], true) || $student->is_profile != 1);
        }
    }

    public function test_admin_can_filter_profiles_via_post(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.student.list'), [
            'is_profile' => '1',
        ]);

        $response->assertStatus(200);
        $response->assertViewHas('students');
        $students = $response->viewData('students');

        foreach ($students as $student) {
            $this->assertEquals(1, $student->is_profile);
        }
    }

    public function test_admin_dashboard_has_profile_completion_metrics(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.admin-dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('completedProfileCount');
        $response->assertViewHas('incompleteProfileCount');
        $response->assertViewHas('paidStudentsCount');
        $response->assertSee('Student Fee Collected / Total');
    }

    public function test_admin_student_list_has_fee_collected_and_total_counts(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.student.list'));

        $response->assertStatus(200);
        $response->assertViewHas('globalPaidStudents');
        $response->assertViewHas('globalCollectedFee');
        $response->assertSee('Fee Collected / Total');
    }

    public function test_admin_can_filter_paid_fee_students(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.student.list', ['fee_status' => 'paid']));

        $response->assertStatus(200);
        $response->assertViewHas('students');
        $students = $response->viewData('students');

        foreach ($students as $student) {
            $hasPaid = $student->examForms->contains(function ($form) {
                return $form->payment && $form->payment->payment_status === 'paid';
            });
            $this->assertTrue($hasPaid);
        }
    }

    public function test_admin_can_filter_unpaid_fee_students(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.student.list', ['fee_status' => 'unpaid']));

        $response->assertStatus(200);
        $response->assertViewHas('students');
        $students = $response->viewData('students');

        foreach ($students as $student) {
            $hasPaid = $student->examForms->contains(function ($form) {
                return $form->payment && $form->payment->payment_status === 'paid';
            });
            $this->assertFalse($hasPaid);
        }
    }
}
