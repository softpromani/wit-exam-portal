<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\student\ExamFormController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Student\StudentController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('login-store', [AuthController::class, 'login'])->name('loginStore');



Route::group(['prefix' => 'student', 'as' => 'student.'], function () {
    Route::get('dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('student-profile', [StudentController::class, 'studentProfile'])->name('profile');
    Route::post('student-profile-store',[StudentController::class,'store'])->name('store');
    Route::post('change-password/{id}',[AuthController::class,'changePassword'])->name('changePassword');
    Route::get('logout', [StudentController::class, 'logout'])->name('logout');
    Route::group(['prefix'=>'semester','as'=>'semester.'],function(){
        Route::get('exam-form',[ExamFormController::class,'exam_form'])->name('exam-form');
        Route::post('subject-fetch',[ExamFormController::class,'subject_fetch'])->name('fetchsubject');
        Route::post('exam-apply',[ExamFormController::class,'apply_exam_form'])->name('exam-apply');
    });
}); 

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('login',[AdminAuthController::class,'login'])->name('admin-login');
    Route::post('login',[AdminAuthController::class,'adminLogin'])->name('admin-login-store');
    Route::get('admin-dashboard',[AdminController::class,'adminDashboard'])->name('admin-dashboard');
    Route::get('logout', [AdminAuthController::class, 'adminLogout'])->name('admin-logout');
});
