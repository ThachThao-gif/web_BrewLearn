<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\LeaderEvaluationController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\TrainingCompletionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

/*
|--------------------------------------------------------------------------
| Global View Composers
|--------------------------------------------------------------------------
*/
View::composer('*', function ($view) {
    if (request()->attributes->has('brewlearn.view-context')) {
        $view->with(request()->attributes->get('brewlearn.view-context'));

        return;
    }
    $currentUser = auth()->user();
    $notifications = collect([]);

    if ($currentUser && Schema::hasTable('notifications')) {
        $query = DB::table('notifications')->where('user_id', $currentUser->id);
        if (in_array(mb_strtolower(trim($currentUser->ChucVu ?? '')), ['quản lý cửa hàng', 'quan ly cua hang', 'manager'])) {
            $query->whereNotIn('type', ['risk', 'prediction']);
        }
        $notifications = $query->orderByDesc('id')->get();
    }

    $context = [
        'currentUser' => $currentUser,
        'notifications' => $notifications,
    ];
    request()->attributes->set('brewlearn.view-context', $context);
    $view->with($context);
});

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (Yêu cầu đăng nhập)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/training/my-assignments', [TrainingCompletionController::class, 'index'])->name('training.assignments');
    Route::post('/training/rounds/{id}/confirm', [TrainingCompletionController::class, 'confirm'])->name('training.confirm');
    Route::prefix('manager')->name('manager.')->controller(ManagerController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/staff', 'staff')->name('staff');
        Route::get('/staff/{id}', 'profile')->name('profile');
        Route::post('/shifts', 'createShift')->name('shifts.create');
        Route::put('/staff/{id}/shift', 'assignShift')->name('shifts.assign');
        Route::put('/staff/{id}/employment', 'updateEmployment')->name('employment.update');
        Route::get('/assessments', 'assessments')->name('assessments');
        Route::post('/assessments/{id}/review', 'review')->name('review');
        Route::get('/training', 'training')->name('training');
        Route::post('/training/{id}/review', 'reviewTraining')->name('training.review');
        Route::put('/training/{id}', 'assignTraining')->name('training.assign');
        Route::post('/training/{id}/reassess', 'reassess')->name('training.reassess');
        Route::get('/notifications', 'notifications')->name('notifications');
    });

    // 1. Trang chủ & Hệ thống
    Route::get('/', [CourseController::class, 'dashboard'])->name('dashboard');
    Route::get('/courses/roadmap', [CourseController::class, 'dashboard'])->name('courses.roadmap');
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Phân hệ Thông báo
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/store', [NotificationController::class, 'store'])->name('store');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('readAll');

        Route::get('/{id}', [NotificationController::class, 'show'])->name('show');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Phân hệ Sự cố / Lỗi
    Route::prefix('incidents')->name('incidents.')->group(function () {
        Route::post('/store', [IncidentController::class, 'store'])->name('store');
    });

    // 2. Phân hệ Khóa học
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/continue', [CourseController::class, 'continueCourse'])->name('continue');
        Route::post('/update-progress', [CourseController::class, 'updateProgress'])->name('updateProgress');
        Route::get('/lesson/{id}', [CourseController::class, 'showLesson'])->name('lesson');
        Route::get('/{id}', [CourseController::class, 'show'])->name('show');
    });
    Route::post('/lessons/{id}/complete', [CourseController::class, 'completeLesson'])->name('lessons.complete');

    // 3. Phân hệ Bài kiểm tra & Sát hạch (Đồng bộ xử lý tại CourseController)
    Route::prefix('tests')->name('tests.')->group(function () {
        Route::get('/', [CourseController::class, 'testsIndex'])->name('index');
        Route::get('/quick-test', [CourseController::class, 'testsIndex'])->name('quick');

        // Trang hiển thị câu hỏi làm bài (Trỏ về showTest trong CourseController)
        Route::get('/{id}', [CourseController::class, 'showTest'])->name('show');

        // Route xử lý nộp bài AJAX (Trỏ về submitTest trong CourseController)
        Route::post('/{id}/submit', [CourseController::class, 'submitTest'])->name('submit');
    });

    // 4. SOP, Hỗ trợ, Chính sách
    Route::view('/sop', 'sop.index')->name('sop.index');
    Route::view('/support', 'support.index')->name('support.index');
    Route::view('/policies', 'policies.index')->name('policies.index');

    Route::get('/assessments', fn () => redirect()->route('tests.index'))->name('assessments.index');
    Route::view('/assessments/3-months', 'assessments.show')->name('assessments.show');

    // 5. Hồ sơ cá nhân & Tài khoản
    Route::get('/staff/{id}/detail', [UserController::class, 'getStaffDetail'])->name('staff.detail');
    Route::get('/profile/edit/{id?}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/profile/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::put('/account/update-contact', [UserController::class, 'updateContact'])->name('account.update-contact');
    Route::put('/account/update-password', [UserController::class, 'updatePassword'])->name('account.update-password');
    Route::post('/account/logout-other-devices', [UserController::class, 'logoutOtherDevices'])->name('account.logout-other-devices');
    Route::put('/account/update-notifications', [UserController::class, 'updateNotifications'])->name('account.update-notifications');
    Route::put('/account/update-preferences', [UserController::class, 'updatePreferences'])->name('account.update-preferences');
    Route::get('/support/hr-request', [SupportController::class, 'hrRequest'])->name('support.hr-request');

    // 6. Phân hệ Quản lý (Trưởng ca)
    Route::prefix('leader')->name('leader.')->group(function () {
        Route::get('/dashboard', [LeaderEvaluationController::class, 'index'])->name('dashboard');
        Route::get('/staff-list', [LeaderEvaluationController::class, 'staffList'])->name('staffList');
        Route::post('/create-schedule', [LeaderEvaluationController::class, 'createSchedule'])->name('createSchedule');
        Route::post('/submit-evaluation/{MaDGNL}', [LeaderEvaluationController::class, 'submitEvaluation'])->name('submitEvaluation');
        Route::post('/save-score', [LeaderEvaluationController::class, 'saveScore'])->name('saveScore');
        Route::post('/send-notification', [LeaderEvaluationController::class, 'sendNotification'])->name('sendNotification');
        Route::post('/record-incident', [LeaderEvaluationController::class, 'recordIncident'])->name('recordIncident');
    });

    // 7. Yêu cầu hỗ trợ Helpdesk
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/', [HelpController::class, 'index'])->name('index');
        Route::post('/store', [HelpController::class, 'store'])->name('store');
    });

});
