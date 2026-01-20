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
use App\Http\Controllers\Admin\AddmissionSessionController;
use App\Http\Controllers\Admin\ExamSessionController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Student\PaymentController as StudentPaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('login-store', [AuthController::class, 'login'])->name(name: 'loginStore');
Route::get('result-view', [AuthController::class, 'resultview'])->name('result');
Route::post('fetch-result', [AuthController::class, 'fetch_result'])->name(name: 'fetch-result');

Route::any('/student-reciept/{id}', [AdminStudentController::class, 'getReciept'])->name('reciept');

Route::group(['prefix' => 'student', 'as' => 'student.', 'middleware' => 'auth:student'], function () {
    Route::get('dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('student-profile', [StudentController::class, 'studentProfile'])->name('profile');
    Route::post('student-profile-store', [StudentController::class, 'store'])->name('store');
    Route::post('student-profile-file-update', [StudentController::class, 'file_update'])->name('file-update');
    Route::post('change-password/{id}', [AuthController::class, 'changePassword'])->name('changePassword');
    Route::get('logout', [StudentController::class, 'logout'])->name('logout');

    Route::group(['prefix' => 'semester', 'as' => 'semester.'], function () {
        Route::get('exam-form', [ExamFormController::class, 'exam_form'])->name('exam-form');
        Route::post('subject-fetch', [ExamFormController::class, 'subject_fetch'])->name('fetchsubject');
        Route::get('exam-for-form/{session_id}/{edit?}', [ExamFormController::class, 'apply_for_exam'])->name('exam-for-apply');
        Route::post('exam-apply', [ExamFormController::class, 'apply_exam_form'])->name('exam-apply');
        Route::get('admitcard-form-list', [ExamFormController::class, 'admitcard_form_list'])->name('admitcard-form-list');
        Route::get('admitcard-download/{exam_session_id}', [ExamFormController::class, 'admitcard_download'])->name('admitcard-download');

        Route::get('examresult-form-list', [ExamFormController::class, 'examresult_form_list'])->name('examresult_form_list');
        Route::get('examresult-download/{exam_session_id}', [ExamFormController::class, 'examresult_download'])->name('examresult-download');

        Route::get('locked-subject-by-examsession/{exam_Session_id}', [ExamFormController::class, 'locked_subject_by_examsession'])->name('locked-subject-by-examsession');
        Route::get('locked-payment-history/{exam_session_id}', [ExamFormController::class, 'locked_payment_history'])->name('locked-payment-history');
    });

    Route::group(['prefix' => 'payment', 'as' => 'payment.'], function () {
        Route::get('pay/{examFormId}', [StudentPaymentController::class, 'payProcess'])->name('process');
        Route::get('checkout/{transactionId}', [StudentPaymentController::class, 'showCheckout'])->name('checkout');
        Route::post('response', [StudentPaymentController::class, 'handlePaymentResponse'])->name('response');
        Route::post('update-status', [StudentPaymentController::class, 'updateAjaxStatus'])->name('update-status');
        Route::post('recheck-status/{transactionId}', [StudentPaymentController::class, 'recheckStatus'])->name('recheck-status');
        Route::get('success', [StudentPaymentController::class, 'success'])->name('success');
        Route::get('failed', [StudentPaymentController::class, 'failed'])->name('failed');
        Route::get('pending', [StudentPaymentController::class, 'pending'])->name('pending');
    });
});

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('login', [AdminAuthController::class, 'login'])->name('admin-login');
    Route::post('login', [AdminAuthController::class, 'adminLogin'])->name('admin-login-store');
});
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth'], function () {
    Route::get('admin-dashboard', [AdminController::class, 'adminDashboard'])->name('admin-dashboard');
    Route::get('logout', [AdminAuthController::class, 'adminLogout'])->name('admin-logout');
    Route::get('exam-form-list', [ExamController::class, 'exam_form_list'])->name('exam-form_list');
    Route::get('exam-form-show/{form_id}', [ExamController::class, 'exam_form_show'])->name('exam-form-show');
    Route::get('exam-schedule-list', [ExamController::class, 'exam_schedule'])->name('exam_schedule_list');
    Route::post('exam-schedule', [ExamController::class, 'exam_schedule_store'])->name('exam_schedule_store');
    Route::get('fetch-exam-schedule', [ExamController::class, 'fetchexam_schedule'])->name('fetch_exam_schedule');
    Route::get('exam-schedule-edit/{id}', [ExamController::class, 'exam_schedule_edit'])->name('exam_schedule_edit');
    Route::post('exam-schedule-update', [ExamController::class, 'exam_schedule_update'])->name('exam_schedule_update');
    Route::get('subject', [ExamController::class, 'subject'])->name('exam_subjects');
    Route::post('fee-payment', [PaymentController::class, 'feePayment'])->name('fee-payment');
    Route::post('exam-exam-session', [ExamController::class, 'ExamSession'])->name('examsession');
    Route::any('attendance_list', [ExamController::class, 'attendanceList'])->name('attendance_list');

    //Marksfeed URI
    Route::any('marksfeed_list', [ExamController::class, 'marksfeedList'])->name('marksfeed_list');
    Route::post('feed-marks', [ExamController::class, 'feedMarks'])->name('feedMarks');
    Route::get('attendance-data/', [ExamController::class, 'attendanceData'])->name('attendance_data');

    //Session
    Route::resource('admission-session', AddmissionSessionController::class);
    Route::resource('exam-session', ExamSessionController::class);
    Route::get('exam-session/{id}/set-amount', [ExamSessionController::class, 'setAmount'])->name('exam-session.set-amount');
    Route::post('exam-session/store-amount', [ExamSessionController::class, 'storeAmount'])->name('exam-session.store-amount');
    Route::post('/update-exam-status/{id}', [ExamSessionController::class, 'updateStatus']);


    // Student Registration
    Route::resource('student', AdminStudentController::class);
    Route::post('student/reset-password/{id}', [AdminStudentController::class, 'resetPassword'])->name('student.reset-password');
    Route::post('student/update-roll-no', [AdminStudentController::class, 'update_roll_no']);
    Route::post('student/import', [AdminStudentController::class, 'import'])->name('student.import');
    Route::any('get-students', [AdminStudentController::class, 'getStudents'])->name('student.list');
    Route::post('student-promote', [AdminStudentController::class, 'promote'])->name('student-promote');


});
