<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Student\ExamFormController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\FeePaymentController;
use App\Http\Controllers\Student\AdmitCardController;

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

Route::group(['prefix' => 'student', 'as' => 'student.','middleware'=>'auth:student'], function () {
    Route::get('dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('student-profile', [StudentController::class, 'studentProfile'])->name('profile');
    Route::post('student-profile-store',[StudentController::class,'store'])->name('store');
    Route::post('change-password/{id}',[AuthController::class,'changePassword'])->name('changePassword');
    Route::get('logout', [StudentController::class, 'logout'])->name('logout');

    Route::group(['prefix'=>'semester','as'=>'semester.'],function(){
        Route::get('exam-form',[ExamFormController::class,'exam_form'])->name('exam-form');
        Route::post('subject-fetch',[ExamFormController::class,'subject_fetch'])->name('fetchsubject');
        Route::get('exam-for-form/{session_id}/{edit?}',[ExamFormController::class,'apply_for_exam'])->name('exam-for-apply');
        Route::post('exam-apply',[ExamFormController::class,'apply_exam_form'])->name('exam-apply');
        Route::get('admitcard-form-list',[ExamFormController::class,'admitcard_form_list'])->name('admitcard-form-list');
        Route::get('admitcard-download/{exam_session_id}',[ExamFormController::class,'admitcard_download'])->name('admitcard-download');
        Route::get('locked-subject-by-examsession/{exam_Session_id}',[ExamFormController::class, 'locked_subject_by_examsession'])->name('locked-subject-by-examsession');

    });
});

Route::group(['prefix' => 'admin', 'as' => 'admin.','middleware'=>'auth'], function () {
    Route::get('login',[AdminAuthController::class,'login'])->name('admin-login');
    Route::post('login',[AdminAuthController::class,'adminLogin'])->name('admin-login-store');
    Route::get('admin-dashboard',[AdminController::class,'adminDashboard'])->name('admin-dashboard');
    Route::get('logout', [AdminAuthController::class, 'adminLogout'])->name('admin-logout');
    Route::get('exam-form-list',[ExamController::class,'exam_form_list'])->name('exam-form_list');
    Route::get('exam-form-show/{form_id}',[ExamController::class,'exam_form_show'])->name('exam-form-show');
    Route::get('exam-schedule-list',[ExamController::class,'exam_schedule'])->name('exam_schedule_list');
    Route::post('exam-schedule',[ExamController::class,'exam_schedule_store'])->name('exam_schedule_store');
    Route::get('fetch-exam-schedule',[ExamController::class,'fetchexam_schedule'])->name('fetch_exam_schedule');
    Route::get('subject',[ExamController::class,'subject'])->name('exam_subjects');
    Route::post('fee-payment',[PaymentController::class,'feePayment'])->name('fee-payment');


});
