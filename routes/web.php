<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\Admin\JuryController;
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
        $query = User::role('student')
            ->where('department_id', $adminDepartment)
            ->whereHas('reports')
            ->with(['filiere', 'reports.teacher']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('matricule', 'like', "%$search%");
            });
        }

        // PAGINATION ICI : 10 par page
        $students = $query->latest()->paginate(10)->withQueryString();

        // Statistiques
        $studentsCount = User::role('student')->where('department_id', $adminDepartment)->count();
        $teachersCount = User::role('teacher')->where('department_id', $adminDepartment)->count();

        $reportsQuery = Report::whereHas('student', function ($q) use ($adminDepartment) {
            $q->where('department_id', $adminDepartment);
        });

        $reportsCount = (clone $reportsQuery)->count();
        $validatedCount = (clone $reportsQuery)->where('status', 'Validé')->count();
        $assignedCount = (clone $reportsQuery)->where('status', 'Affecté')->count();
        $commentedCount = (clone $reportsQuery)->where('status', 'commenté')->count();
        $submittedCount = (clone $reportsQuery)->where('status', 'Soumis')->count();

        return view('dashboard', compact(
            'students',
            'studentsCount',
            'teachersCount',
            'reportsCount',
            'validatedCount',
            'assignedCount',
            'commentedCount',
            'submittedCount'
        ));
    }

    if ($user->hasRole('student')) {
        $reports = Report::where('student_id', $user->id)
            ->with(['versions', 'teacher'])
            ->latest()
            ->get();
        $latestReport = $reports->first();
        return view('student.dashboard', compact('reports', 'latestReport'));
    }

    if ($user->hasRole('teacher')) {
        $query = Report::where('teacher_id', $user->id)
            ->with(['student.filiere', 'versions']);

        if ($request->filled('filiere')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('filiere_id', $request->filiere);
            });
        }

        // PAGINATION ICI AUSSI : 10 par page
        $assignedReports = $query->latest()->paginate(10)->withQueryString();

        // Pour les stats, on récupère le total sans la pagination pour les compteurs
        $allReports = Report::where('teacher_id', $user->id)->get();

        $stats = [
            'pending' => $allReports->whereIn('status', [
                Report::STATUS_SUBMITTED,
                Report::STATUS_COMMENTED,
                Report::STATUS_ASSIGNED
            ])->count(),

            'validated' => $allReports->where('status', Report::STATUS_VALIDATED)->count(),

            'finished' => $allReports->where('status', Report::STATUS_FINAL)->count(),
        ];

        $filieres = Filiere::where('department_id', $user->department_id)->get();

        return view('teacher.dashboard', compact('assignedReports', 'stats', 'filieres'));
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
        Route::patch('/update', [ProfileController::class, 'updateUser'])->name('student.update');
        Route::get('/profile', [ProfileController::class, 'studentProfile'])->name('profile.student');
    });

    // SUPERADMIN (avec middleware role)
    Route::prefix('superadmin')
        ->name('superadmin.')
        ->middleware('role:superadmin')
        ->group(function () {
            //Route::get('/users', [AdminController::class, 'superadminUsers'])->name('users');
            Route::get('/reports', [ReportController::class, 'superadminReports'])->name('reports');

            Route::get('/system', [AdminController::class, 'systemConfig'])->name('system');
            Route::get('/stats', [ReportController::class, 'superadminStats'])->name('stats');
            Route::get('/students', [UserController::class, 'studentsIndex'])->name('students.index');
            Route::get('/teachers', [UserController::class, 'teachersIndex'])->name('teachers.index');
            Route::get('/users', [UserController::class, 'usersIndex'])->name('users.index');

            Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

            // Filtres pour enseignants
            Route::get('/teachers/{user}/edit', [UserController::class, 'editTeacher'])->name('teachers.edit');
            Route::patch('/teachers/{user}', [UserController::class, 'updateTeacher'])->name('teachers.update');
            Route::delete('/teachers/{user}', [UserController::class, 'destroyTeacher'])->name('teachers.destroy');
            Route::get('/students/{user}/edit', [UserController::class, 'editStudent'])->name('students.edit');
            Route::patch('/students/{user}', [UserController::class, 'updateStudent'])->name('students.update');
            Route::delete('/students/{user}', [UserController::class, 'destroyStudent'])->name('students.destroy');

            Route::get('/admins', [UserController::class, 'adminsIndex'])->name('admins.index');
            Route::get('/admins/{user}/edit', [UserController::class, 'editAdmin'])->name('admins.edit');
            Route::patch('/admins/{user}', [UserController::class, 'updateAdmin'])->name('admins.update');
            Route::delete('/admins/{user}', [UserController::class, 'destroyAdmin'])->name('admins.destroy');
            Route::get('/admins/create', [UserController::class, 'createAdmin'])->name('admins.create');
            Route::post('/admins', [UserController::class, 'storeAdmin'])->name('admins.store');
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
            Route::get('/students', [AdminController::class, 'studentsIndex'])->name('students.index');
            Route::get('/students/{user}/edit', [UserController::class, 'editStudent'])->name('students.edit');
            Route::patch('/students/{user}', [UserController::class, 'updateStudent'])->name('students.update');
            Route::delete('/students/{user}', [AdminController::class, 'destroyUser'])->name('students.destroy');
            Route::get('/teachers', [AdminController::class, 'teachersIndex'])->name('teachers.index');
            Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
            Route::patch('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
            Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
            Route::get('/profile', [ProfileController::class, 'adminProfile'])->name('profile.admin');
            Route::prefix('juries')
                ->name('juries.')
                ->controller(\App\Http\Controllers\Admin\JuryController::class)
                ->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/', 'store')->name('store');
                    Route::put('/{jury}', 'update')->name('update');
                    Route::delete('/{jury}', 'destroy')->name('destroy');
                });
            Route::patch('/update', [ProfileController::class, 'updateUser'])->name('admin.update');
            Route::post('/reports/add-jury-member', [ReportController::class, 'addJuryMember'])->name('reports.addJuryMember');
            Route::post('/juries/add-member', [JuryController::class, 'addJuryMember'])->name('juries.addMember');

            Route::post('/reports/add-jury-member', [ReportController::class, 'addJuryMember'])->name('reports.addJuryMember');
            Route::post('/juries/add-member', [JuryController::class, 'addJuryMember'])->name('juries.addMember');

        });



    // ENSEIGNANT
    Route::prefix('teacher')->name('teacher.')->middleware('role:teacher')->group(function () {
        Route::get('/profile', [TeacherController::class, 'showProfile'])->name('profile');
        Route::patch('/profile', [TeacherController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [TeacherController::class, 'updatePassword'])->name('profile.password');
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
    Route::post('/reports/{report}/remove-teacher', [ReportController::class, 'removeTeacher']);

    //Action filière

});
require __DIR__ . '/auth.php';