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
        $response->assertSee('Completed');
        $response->assertSee('Incomplete');
    }
}
