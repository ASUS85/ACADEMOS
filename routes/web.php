<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Models\Filiere;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Report;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/filieres/{department}', function ($departmentId) {
    return Filiere::where('department_id', $departmentId)->get();
});

Route::get('/dashboard', function (Request $request) {
    $user = auth()->user();
    $adminDepartment = $user->department_id;

    if ($user->hasAnyRole(['admin', 'superadmin'])) {
        // 1. Requête pour le tableau des étudiants du département
        $query = User::role('student')
            ->where('department_id', $adminDepartment)
            ->with(['filiere', 'reports.teacher']); // On charge le rapport et l'encadreur lié

        // RECHERCHE SIMPLE (Nom ou Matricule)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('matricule', 'like', "%$search%");
            });
        }

        $students = $query->latest()->get();

        // 2. Statistiques (uniquement pour le département)
        $studentsCount = User::role('student')->where('department_id', $adminDepartment)->count();
        $teachersCount = User::role('teacher')->where('department_id', $adminDepartment)->count();

        $reportsQuery = Report::whereHas('student', function ($q) use ($adminDepartment) {
            $q->where('department_id', $adminDepartment);
        });

        $reportsCount = (clone $reportsQuery)->count();
        $validatedCount = (clone $reportsQuery)->where('status', 'Validé final')->count();
        $modifiedCount = (clone $reportsQuery)->where('status', 'modifié')->count();
        $commentedCount = (clone $reportsQuery)->where('status', 'commenté')->count();

        return view('dashboard', compact(
            'students',
            'studentsCount',
            'teachersCount',
            'reportsCount',
            'validatedCount',
            'modifiedCount',
            'commentedCount'
        ));
    }

     if ($user->hasRole('student')) {
        $myReports = \App\Models\Report::where('student_id', $user->id)->latest()->get();
        return view('student.dashboard', compact('myReports'));
    }

    // 3. Si c'est un ENSEIGNANT
    if ($user->hasRole('teacher')) {
        $assignedReports = \App\Models\Report::where('teacher_id', $user->id)->get();
        return view('teacher.dashboard', compact('assignedReports'));
    }

    return redirect('/');
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
        ->middleware('role:superadmin')
        ->group(function () {
            //Route::get('/users', [AdminController::class, 'superadminUsers'])->name('users');
            Route::get('/reports', [ReportController::class, 'superadminReports'])->name('reports');
            Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
            Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
            Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
            Route::get('/system', [AdminController::class, 'systemConfig'])->name('system');
            Route::get('/stats', [ReportController::class, 'superadminStats'])->name('stats');
            Route::get('/users', [UserController::class, 'index'])->name('users');
            Route::resource('admins', AdminController::class);
        });

    // ADMIN (avec middleware role)
    Route::prefix('admin')
        ->name('admin.')
        ->middleware(['auth', 'role:admin|superadmin'])
        ->group(function () {
            Route::get('/reports', [ReportController::class, 'adminIndex'])->name('reports.index');
            Route::get('/users', [AdminController::class, 'AdmineditUsers'])->name('users.index');
            Route::get('/stats', [ReportController::class, 'adminStats'])->name('stats.index');
            Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
            Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
            Route::resource('teachers', TeacherController::class);
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

    //Action filière

});

require __DIR__ . '/auth.php';
