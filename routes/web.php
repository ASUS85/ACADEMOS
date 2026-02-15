<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/student/reports/create', [ReportController::class, 'create']);
    Route::post('/student/reports', [ReportController::class, 'store']);
    Route::get('/admin/reports', [ReportController::class, 'adminIndex'])->name('admin.reports');
    Route::post('/reports/{report}/assign', [ReportController::class, 'assign'])->name('reports.assign');
    Route::get('/teacher/reports', [ReportController::class, 'teacherIndex'])->name('teacher.reports');
    Route::post('/reports/{report}/teacher-comment', [ReportController::class, 'teacherComment'])->name('reports.teacher-comment');
    Route::post('/reports/{report}/assign-jury', [ReportController::class, 'assignJury'])->name('reports.assign-jury');
    Route::get('/jury/reports', [ReportController::class, 'juryIndex'])->name('jury.reports');
    Route::post('/reports/{report}/jury-evaluate', [ReportController::class, 'juryEvaluate'])->name('reports.jury-evaluate');
});

require __DIR__ . '/auth.php';
