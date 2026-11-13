<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\LeaderEvaluationController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TrainingCompletionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Trang chủ và tìm kiếm
    Route::get('/', [CourseController::class, 'dashboard'])->name('dashboard');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Học tập: khóa học, bài học, bài kiểm tra
    Route::prefix('courses')->name('courses.')->controller(CourseController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/update-progress', 'updateProgress')->name('updateProgress');
        Route::get('/lesson/{id}', 'showLesson')->whereNumber('id')->name('lesson');
        Route::get('/{id}', 'show')->whereNumber('id')->name('show');
    });

    Route::prefix('tests')->name('tests.')->controller(CourseController::class)->group(function () {
        Route::get('/', 'testsIndex')->name('index');
        Route::get('/{id}', 'showTest')->whereNumber('id')->name('show');
        Route::post('/{id}/submit', 'submitTest')->whereNumber('id')->name('submit');
    });

    Route::view('/assessments/3-months', 'assessments.show')->name('assessments.show');
    Route::view('/policies', 'policies.index')->name('policies.index');

    // Thông báo
    Route::prefix('notifications')->name('notifications.')->controller(NotificationController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/read-all', 'markAllAsRead')->name('readAll');
        Route::get('/{id}', 'show')->whereNumber('id')->name('show');
        Route::post('/{id}/read', 'markAsRead')->whereNumber('id')->name('read');
        Route::delete('/{id}', 'destroy')->whereNumber('id')->name('destroy');
    });

    // Tài khoản cá nhân
    Route::get('/profile/edit/{id?}', [UserController::class, 'edit'])->whereNumber('id')->name('users.edit');
    Route::prefix('account')->name('account.')->controller(UserController::class)->group(function () {
        Route::put('/update-contact', 'updateContact')->name('update-contact');
        Route::put('/update-password', 'updatePassword')->name('update-password');
        Route::post('/logout-other-devices', 'logoutOtherDevices')->name('logout-other-devices');
        Route::put('/update-notifications', 'updateNotifications')->name('update-notifications');
        Route::put('/update-preferences', 'updatePreferences')->name('update-preferences');
    });

    // Yêu cầu hỗ trợ
    Route::get('/help', [HelpController::class, 'index'])->name('help.index');
    Route::post('/help/store', [HelpController::class, 'store'])->name('help.store');

    // Đào tạo lại: người phụ trách xác nhận hoàn thành vòng
    Route::get('/training/my-assignments', [TrainingCompletionController::class, 'index'])->name('training.assignments');
    Route::post('/training/rounds/{id}/confirm', [TrainingCompletionController::class, 'confirm'])->whereNumber('id')->name('training.confirm');

    // Trưởng ca
    Route::prefix('leader')->name('leader.')->controller(LeaderEvaluationController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/scoring', 'index')->name('scoring');
        Route::get('/staff-list', 'staffList')->name('staffList');
        Route::get('/staff/{id}', 'staffProfile')->whereNumber('id')->name('staffProfile');
        Route::post('/create-schedule', 'createSchedule')->name('createSchedule');
        Route::post('/save-score', 'saveScore')->name('saveScore');
        Route::post('/send-notification', 'sendNotification')->name('sendNotification');
        Route::post('/record-incident', 'recordIncident')->name('recordIncident');
    });

    // Quản lý cửa hàng
    Route::prefix('manager')->name('manager.')->controller(ManagerController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/staff', 'staff')->name('staff');
        Route::get('/staff/{id}', 'profile')->whereNumber('id')->name('profile');
        Route::post('/shifts', 'createShift')->name('shifts.create');
        Route::put('/staff/{id}/shift', 'assignShift')->whereNumber('id')->name('shifts.assign');
        Route::put('/staff/{id}/employment', 'updateEmployment')->whereNumber('id')->name('employment.update');
        Route::get('/assessments', 'assessments')->name('assessments');
        Route::post('/assessments/{id}/review', 'review')->whereNumber('id')->name('review');
        Route::get('/training', 'training')->name('training');
        Route::put('/training/{id}', 'assignTraining')->whereNumber('id')->name('training.assign');
        Route::post('/training/{id}/reassess', 'reassess')->whereNumber('id')->name('training.reassess');
        Route::get('/notifications', 'notifications')->name('notifications');
    });
});
