<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Filiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // === METHODES FACTORISEES ===

    private function getFilteredUsers($role, Request $request, $perPage = 20)
    {
        $query = User::role($role)->with(['department', 'filiere', 'roles']);

        // Filtres communs
        $this->applyCommonFilters($query, $request);

        // Filtres spécifiques
        match ($role) {
            'student' => $this->applyStudentFilters($query, $request),
            'teacher' => $this->applyTeacherFilters($query, $request),
            'admin'   => $this->applyAdminFilters($query, $request),
            default   => null
        };

        $users = $query->latest()->paginate($perPage)->withQueryString();

        $departments = Department::withCount([
            'users as total' => fn($q) => $q->role($role)
        ])->get();

        $filieres = Filiere::withCount([
            'users as total' => fn($q) => $q->role($role)
        ])->get();
        return compact('users', 'departments', 'filieres');
    }

    private function applyCommonFilters($query, Request $request)
    {
        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('search')) {
            $query->where(
                fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
            );
        }
    }

    private function applyStudentFilters($query, Request $request)
    {
        if ($request->filled('filiere')) {
            $query->where('filiere_id', $request->filiere);
        }

        if ($request->filled('niveau')) {
            $query->where('niveau', $request->niveau);
        }

        if ($request->filled('matricule')) {
            $query->where('matricule', 'like', "%{$request->matricule}%");
        }
    }

    private function applyTeacherFilters($query, Request $request)
    {
        if ($request->filled('filiere')) {
            $query->where('filiere_id', $request->filiere);
        }

        if ($request->filled('matiere')) {
            $query->where('specialite', $request->matiere);
        }
    }

    private function applyAdminFilters($query, Request $request)
    {
        // Aucun filtre spécifique
    }

    // === DASHBOARD ===

    public function superadminDashboard()
    {
        $stats = [
            'totalUsers' => User::count(),
            'totalStudents' => User::role('student')->count(),
            'totalTeachers' => User::role('teacher')->count(),
            'totalAdmins' => User::role('admin')->count(),
            'totalDepartments' => Department::count(),
        ];

        $students = User::role('student')
            ->whereHas('reports')
            ->with(['department', 'filiere'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'students'));
    }

    // === LISTES ===

    public function studentsIndex(Request $request)
    {
        [
            'users' => $students,
            'departments' => $departments,
            'filieres' => $filieres
        ] = $this->getFilteredUsers('student', $request);

        return view('admin.superadmin.listStudent', compact('students', 'departments', 'filieres'));
    }

    public function teachersIndex(Request $request)
    {
        [
            'users' => $teachers,
            'departments' => $departments,
            'filieres' => $filieres
        ] = $this->getFilteredUsers('teacher', $request);

        return view('admin.superadmin.listTeacher', compact('teachers', 'departments', 'filieres'));
    }

    public function adminsIndex(Request $request)
    {
        [
            'users' => $admins,
            'departments' => $departments
        ] = $this->getFilteredUsers('admin', $request);

        return view('admin.superadmin.listAdmin', compact('admins', 'departments'));
    }

    public function usersIndex(Request $request)
    {
        $query = User::with(['department', 'roles']);

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('search')) {
            $query->where(
                fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
            );
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $departments = Department::all();

        return view('admin.superadmin.listUsers', compact('users', 'departments'));
    }

    // === CRUD ETUDIANTS ===

    public function editStudent(User $user)
    {
        if (!$user->hasRole('student')) abort(403);

        $user->load(['department', 'filiere']);
        $departments = Department::all();
        $filieres = Filiere::where('department_id', $user->department_id)->get();

        return view('superadmin.students.edit', compact('user', 'departments', 'filieres'));
    }

    public function updateStudent(Request $request, User $user)
    {
        
        $user->update($request->only([
            'name',
            'email',
            'matricule',
            'niveau',
            'filiere_id',
            'department_id',
            'sexe'
        ]));

         return back()->with('success', 'Étudiant mis à jour avec succès ✅');
    }

    public function destroyStudent(User $user)
    {
        if (!$user->hasRole('student')) abort(403);

        $user->delete();

        return back()->with('success', '✅ Étudiant supprimé');
    }

    // === CRUD ENSEIGNANTS ===

    public function editTeacher(User $user)
    {
        if (!$user->hasRole('teacher')) abort(403);

        $user->load(['department', 'filiere']);
        $departments = Department::all();
        $filieres = Filiere::where('department_id', $user->department_id)->get();

        return view('superadmin.teachers.edit', compact('user', 'departments', 'filieres'));
    }

    public function updateTeacher(Request $request, User $user)
    {
        if (!$user->hasRole('teacher')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'matricule' => 'nullable|string|max:50|unique:users,matricule,' . $user->id,
            'grade' => 'nullable|string|max:20',
            'specialite' => 'nullable|string|max:100',
            'filiere_id' => 'nullable|exists:filieres,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $user->update($request->only([
            'name',
            'email',
            'matricule',
            'grade',
            'specialite',
            'filiere_id',
            'department_id'
        ]));

        return redirect()->route('superadmin.teachers.index')->with('success', '✅ Enseignant mis à jour');
    }

    public function destroyTeacher(User $user)
    {
        if (!$user->hasRole('teacher')) abort(403);

        $user->delete();

        return back()->with('success', '✅ Enseignant supprimé');
    }

    // === CRUD ADMINS ===

    public function createAdmin()
    {
        $departments = Department::all();
        return view('superadmin.admins.create', compact('departments'));
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'department_id' => 'required|exists:departments,id',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
        ]);

        $admin->assignRole('admin');

        return redirect()->route('superadmin.admins.index')->with('success', '✅ Admin créé');
    }

    public function editAdmin(User $user)
    {
        if (!$user->hasRole('admin')) abort(403);

        $user->load('department');
        $departments = Department::all();

        return view('superadmin.admins.edit', compact('user', 'departments'));
    }

    public function updateAdmin(Request $request, User $user)
    {
        if (!$user->hasRole('admin')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'department_id' => 'required|exists:departments,id',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $data = $request->only(['name', 'email', 'department_id']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('superadmin.admins.index')->with('success', '✅ Admin mis à jour');
    }

    public function destroyAdmin(User $user)
    {
        if (!$user->hasRole('admin')) abort(403);

        if (auth()->id() === $user->id) {
            return back()->with('error', '❌ Auto-suppression interdite');
        }

        $user->delete();

        return back()->with('success', '✅ Admin supprimé');
    }
}
