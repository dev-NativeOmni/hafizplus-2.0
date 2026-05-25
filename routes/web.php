<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HafalanRecordController;
use App\Http\Controllers\HafalanTargetController;
use App\Http\Controllers\MurajaahRecordController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\QuickInputController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SystemNotificationController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'redirect'])
        ->name('dashboard');

    Route::get('/super-admin/dashboard', [DashboardController::class, 'superAdmin'])
        ->middleware('role:super_admin')
        ->name('super-admin.dashboard');

    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('role:super_admin,admin')
        ->name('admin.dashboard');

    Route::get('/teacher/dashboard', [DashboardController::class, 'teacher'])
        ->middleware('role:teacher')
        ->name('teacher.dashboard');

    Route::get('/parent/dashboard', [DashboardController::class, 'parent'])
        ->middleware('role:parent')
        ->name('parent.dashboard');

    Route::get('/student/dashboard', [DashboardController::class, 'student'])
        ->middleware('role:student')
        ->name('student.dashboard');

    /*
    |--------------------------------------------------------------------------
    | System Notifications
    |--------------------------------------------------------------------------
    | Index/show/read/delete boleh diakses semua user login.
    | Create/store/edit/update hanya admin dan super admin.
    |--------------------------------------------------------------------------
    */

    Route::prefix('system-notifications')
        ->name('system-notifications.')
        ->group(function () {
            Route::get('/', [SystemNotificationController::class, 'index'])
                ->name('index');

            Route::patch('/mark-all-read', [SystemNotificationController::class, 'markAllAsRead'])
                ->name('mark-all-read');

            Route::middleware(['role:super_admin,admin'])->group(function () {
                Route::get('/create', [SystemNotificationController::class, 'create'])
                    ->name('create');

                Route::post('/', [SystemNotificationController::class, 'store'])
                    ->name('store');

                Route::get('/{systemNotification}/edit', [SystemNotificationController::class, 'edit'])
                    ->name('edit');

                Route::patch('/{systemNotification}', [SystemNotificationController::class, 'update'])
                    ->name('update');
            });

            Route::get('/{systemNotification}', [SystemNotificationController::class, 'show'])
                ->name('show');

            Route::patch('/{systemNotification}/mark-as-read', [SystemNotificationController::class, 'markAsRead'])
                ->name('mark-as-read');

            Route::delete('/{systemNotification}', [SystemNotificationController::class, 'destroy'])
                ->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | Admin Area
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:super_admin,admin'])->group(function () {
        Route::resource('programs', ProgramController::class);
        Route::resource('class-rooms', ClassRoomController::class);
        Route::resource('teachers', TeacherController::class);
        Route::resource('parents', ParentController::class);
        Route::resource('students', StudentController::class);

        Route::get('/audit-logs', [AuditLogController::class, 'index'])
            ->name('audit-logs.index');

        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])
            ->name('audit-logs.show');

        Route::prefix('database-backups')
            ->name('database-backups.')
            ->group(function () {
                Route::get('/', [DatabaseBackupController::class, 'index'])
                    ->name('index');

                Route::post('/', [DatabaseBackupController::class, 'store'])
                    ->name('store');

                Route::get('/{filename}/download', [DatabaseBackupController::class, 'download'])
                    ->name('download');

                Route::delete('/{filename}', [DatabaseBackupController::class, 'destroy'])
                    ->name('destroy');
            });
    });

    /*
    |--------------------------------------------------------------------------
    | Hafalan, Murajaah, Target, Quick Input
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:super_admin,admin,teacher'])->group(function () {
        Route::resource('hafalan-records', HafalanRecordController::class);
        Route::resource('murajaah-records', MurajaahRecordController::class);

        Route::patch('/hafalan-targets/{hafalanTarget}/complete', [HafalanTargetController::class, 'complete'])
            ->name('hafalan-targets.complete');

        Route::patch('/hafalan-targets/{hafalanTarget}/mark-missed', [HafalanTargetController::class, 'markMissed'])
            ->name('hafalan-targets.mark-missed');

        Route::resource('hafalan-targets', HafalanTargetController::class);

        Route::get('/quick-inputs', [QuickInputController::class, 'index'])
            ->name('quick-inputs.index');

        Route::post('/quick-inputs/hafalan', [QuickInputController::class, 'storeHafalan'])
            ->name('quick-inputs.hafalan.store');

        Route::post('/quick-inputs/murajaah', [QuickInputController::class, 'storeMurajaah'])
            ->name('quick-inputs.murajaah.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Progress & Reports
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:super_admin,admin,teacher,parent,student'])->group(function () {
        Route::get('/progress', [ProgressController::class, 'index'])
            ->name('progress.index');

        Route::get('/progress/{student}', [ProgressController::class, 'show'])
            ->name('progress.show');

        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');

        Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])
            ->name('reports.export.csv');

        Route::get('/reports/student/{student}', [ReportController::class, 'student'])
            ->name('reports.student');

        Route::get('/reports/student/{student}/export/csv', [ReportController::class, 'exportStudentCsv'])
            ->name('reports.student.export.csv');
    });

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__ . '/auth.php';