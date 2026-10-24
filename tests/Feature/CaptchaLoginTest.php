<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CaptchaLoginTest extends TestCase
{
    public function test_captcha_image_endpoint_returns_png_and_sets_session(): void
    {
        $response = $this->get(route('captcha.image'));

        $response->assertStatus(200);
        $this->assertEquals('image/png', $response->headers->get('Content-Type'));
        $this->assertNotEmpty(session('captcha_code'));
    }

    public function test_captcha_refresh_endpoint_returns_json(): void
    {
        $response = $this->get(route('captcha.refresh'));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
        ]);
        $this->assertStringContainsString('captcha-image', $response->json('url'));
    }

    public function test_student_login_fails_without_captcha(): void
    {
        $response = $this->post(route('loginStore'), [
            'university_roll_no' => 'WIT12345',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors(['captcha']);
    }

    public function test_student_login_fails_with_invalid_captcha(): void
    {
        $this->withSession(['captcha_code' => 'ABCDE']);

        $response = $this->post(route('loginStore'), [
            'university_roll_no' => 'WIT12345',
            'password' => 'secret123',
            'captcha' => 'WRONG',
        ]);

        $response->assertSessionHasErrors(['captcha']);
    }

    public function test_student_login_accepts_valid_captcha(): void
    {
        $this->withSession(['captcha_code' => 'ABCDE']);

        $response = $this->post(route('loginStore'), [
            'university_roll_no' => 'WIT12345',
            'password' => 'secret123',
            'captcha' => 'abcde', // case-insensitive
        ]);

        // Captcha validation passed; credentials check fails or succeeds (no captcha error)
        $response->assertSessionDoesntHaveErrors(['captcha']);
    }

    public function test_admin_login_fails_without_captcha(): void
    {
        $response = $this->post(route('admin.admin-login-store'), [
            'email' => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors(['captcha']);
    }

    public function test_admin_login_fails_with_invalid_captcha(): void
    {
        $this->withSession(['captcha_code' => 'ABCDE']);

        $response = $this->post(route('admin.admin-login-store'), [
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'captcha' => 'WRONG',
        ]);

        $response->assertSessionHasErrors(['captcha']);
    }

    public function test_admin_login_accepts_valid_captcha(): void
    {
        $this->withSession(['captcha_code' => 'ABCDE']);

        $response = $this->post(route('admin.admin-login-store'), [
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'captcha' => 'abcde', // case-insensitive
        ]);

        // Captcha validation passed; credentials check fails or succeeds (no captcha error)
        $response->assertSessionDoesntHaveErrors(['captcha']);
    }

    public function test_admin_login_succeeds_with_new_password_and_valid_captcha(): void
    {
        $this->withSession(['captcha_code' => 'TEST1']);

        $response = $this->post(route('admin.admin-login-store'), [
            'email' => 'admin@witlnmu.ac.in',
            'password' => 'Witlnmu@2026',
            'captcha' => 'TEST1',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect(route('admin.admin-dashboard'));
        $this->assertAuthenticatedAs(User::where('email', 'admin@witlnmu.ac.in')->first());
    }
}
