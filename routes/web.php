<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // PROFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ÉTUDIANT
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
        Route::get('/dashboard', [ReportController::class, 'studentDashboard'])->name('dashboard');
        Route::post('/reports/{report}/resubmit', [ReportController::class, 'resubmit'])->name('resubmit');
    });

    // SUPERADMIN (avec middleware role)
    Route::prefix('superadmin')
        ->name('superadmin.')
        ->middleware('role:admin|superadmin')
        ->group(function () {
            Route::get('/users', [AdminController::class, 'superadminUsers'])->name('users');
            Route::get('/reports', [AdminController::class, 'superadminReports'])->name('reports');
            Route::get('/system', [AdminController::class, 'systemConfig'])->name('system');
            Route::get('/stats', [AdminController::class, 'superadminStats'])->name('stats');
            Route::resource('admins', AdminController::class);
        });

    // ADMIN (avec middleware role)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/reports', [ReportController::class, 'adminIndex'])->name('reports.index');
        Route::get('/users', [ReportController::class, 'adminUsers'])->name('users.index');
        Route::get('/stats', [ReportController::class, 'adminStats'])->name('stats.index');
        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    });



    // ENSEIGNANT
    Route::prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/reports', [ReportController::class, 'teacherIndex'])->name('reports.index');
    });

    // JURY
    Route::prefix('jury')->name('jury.')->group(function () {
        Route::get('/reports', [ReportController::class, 'juryIndex'])->name('reports.index');
    });

    // ACTIONS RAPPORTS (communes)
    Route::post('/reports/{report}/assign', [ReportController::class, 'assign'])->name('reports.assign');
    Route::post('/reports/{report}/teacher-comment', [ReportController::class, 'teacherComment'])->name('reports.teacher-comment');
    Route::post('/reports/{report}/assign-jury', [ReportController::class, 'assignJury'])->name('reports.assign-jury');
    Route::post('/reports/{report}/jury-evaluate', [ReportController::class, 'juryEvaluate'])->name('reports.jury-evaluate');
});

require __DIR__ . '/auth.php';
