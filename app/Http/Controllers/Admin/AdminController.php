<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Department;
use App\Models\Filiere;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Matiere;

class AdminController extends Controller
{
    // Liste des admins
    public function index()
    {
        $admins = User::role('admin')->with('roles')->paginate(10);
        return view('admin.admins.index', compact('admins'));
    }

    // Création admin
    public function create()
    {
        if (!auth()->user()->hasRole('superadmin')) abort(403);

        $departments = Department::all();
        return view('admin.admins.create', compact('departments'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('superadmin')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'department_id' => 'required|exists:departments,id',
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
        ]);

        $admin->assignRole('admin');

        return redirect()->route('superadmin.admins.index')
            ->with('success', '✅ Admin créé avec succès');
    }

    // Liste de tous les utilisateurs (superadmin)
    public function superadminUsers()
    {
        $users = User::with('roles')->paginate(20);
        return view('admin.superadmin.users', compact('users'));
    }

    // Éditer un utilisateur (superadmin)
    public function editUser(User $user)
    {
        $user->load('department', 'roles');
        $departments = Department::all();
        $filieres = Filiere::all();
        $roles = ['superadmin', 'admin', 'teacher', 'student', 'jury'];

        return view('admin.superadmin.edit-user', compact('user', 'departments', 'roles', 'filieres'));
    }

    // Mettre à jour un utilisateur
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'matricule' => 'nullable|string|max:50',
            'specialite' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'nullable|string',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'matricule' => $request->matricule,
            'specialite' => $request->specialite,
            'department_id' => $request->department_id,
        ]);

        if ($request->role) {
            $user->syncRoles([$request->role]);
        }

        return redirect()->route('superadmin.users')
            ->with('success', 'Utilisateur mis à jour avec succès ✅');
    }

    // Supprimer un utilisateur
    public function destroyUser(User $user)
    {
        $user->delete();

        return back()->with('success', 'Utilisateur supprimé avec succès ✅');
    }

    // Gestion des rapports superadmin
    public function superadminReports()
    {
        $reports = Report::with(['student', 'latestVersion'])->latest()->paginate(25);
        $stats = [
            'total' => Report::count(),
            'pending' => Report::where('status', '!=', 'Validé final')->count(),
        ];
        return view('admin.superadmin.reports', compact('reports', 'stats'));
    }

    // Autres méthodes existantes

    public function storeTeacher(Request $request)
    {
        $teacher = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'department_id' => auth()->user()->department_id
        ]);
        $teacher->assignRole('teacher');
        $teacher->filieres()->sync($request->filieres);
    }

    public function systemConfig()
    {
        return view('admin.superadmin.system');
    }

    public function AdmineditUsers(Request $request)
    {
        $adminDepartment = auth()->user()->department_id;

        $users = User::query();

        //  Limiter au département de l'admin
        $users->where('department_id', $adminDepartment)
             ->with(['filiere', 'matieres']);

        // Ne montrer que students + teachers
        $users->whereHas('roles', function ($q) {
            $q->whereIn('name', ['student', 'teacher']);
        });

        //  Filtre role
        if ($request->role) {
            $users->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        //  Filtre spécialité
        if ($request->specialite) {
            $users->where('specialite', $request->specialite);
        }

         // Filtre matière
        if ($request->filled('matiere')) {
            $users->whereHas('matieres', function ($q) use ($request) {
                $q->where('matieres.id', $request->matiere);
            });
        }

        $users = $users->with(['roles', 'department', 'filiere'])->latest()->paginate(10);
        $filieres      = Filiere::where('department_id', $adminDepartment)->get();
        $matieres      = Matiere::whereIn('filiere_id', $filieres->pluck('id'))->get();

        return view('admin.admins.listUsers', compact('users', 'filieres', 'matieres'));
    }

    public function studentsIndex(Request $request)
    {
        $adminDepartment = auth()->user()->department_id;

        $query = User::role('student')
            ->where('department_id', $adminDepartment)
            ->with('filiere');

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('matricule', 'like', "%$search%");
            });
        }

        // Filtre filière
        if ($request->filled('filiere')) {
            $query->where('filiere_id', $request->filiere);
        }

        $students = $query->latest()->paginate(15)->withQueryString();
        $filieres = Filiere::where('department_id', $adminDepartment)->get();
        $studentsCount = $query->count();

        return view('admin.admins.listStudent', compact('students', 'filieres', 'studentsCount'));
    }


    public function teachersIndex(Request $request)
    {
        $adminDepartment = auth()->user()->department_id;

        $query = User::role('teacher')
            ->where('department_id', $adminDepartment)
            ->with(['filiere', 'matieres']); // charger aussi les matières

        // Recherche par nom/email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        // Filtre filière
        if ($request->filled('filiere')) {
            $query->whereHas('filieres', function ($q) use ($request) {
                $q->where('filieres.id', $request->filiere);
            });
        }


        // Filtre matière
        if ($request->filled('matiere')) {
            $query->whereHas('matieres', function ($q) use ($request) {
                $q->where('matieres.id', $request->matiere);
            });
        }

        $teachers      = $query->latest()->paginate(15)->withQueryString();
        $filieres      = Filiere::where('department_id', $adminDepartment)->get();
        $matieres      = Matiere::whereIn('filiere_id', $filieres->pluck('id'))->get();
        $teachersCount = $query->count();

        return view('admin.admins.listTeacher', compact('teachers', 'filieres', 'matieres', 'teachersCount'));
    }



    public function editStudent(User $student)
    {
        // Charger les relations utiles
        $student->load('department', 'filiere', 'roles');

        // Récupérer les filières du département de l’admin
        $filieres = Filiere::where('department_id', auth()->user()->department_id)->get();

        // Récupérer les départements
        $departments = Department::all();

        // Liste des niveaux possibles (tu peux adapter selon ton école)
        $niveaux = ['Licence 1', 'Licence 2', 'Licence 3', 'Master 1', 'Master 2'];

        return view('student.editStudent', compact('student', 'filieres', 'departments', 'niveaux'));
    }

    public function updateStudent(Request $request, User $student)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $student->id,
            'filiere_id' => 'nullable|exists:filieres,id',
            'niveau'     => 'nullable|string|max:50',
        ]);

        $student->update([
            'name'       => $request->name,
            'email'      => $request->email,
            'filiere_id' => $request->filiere_id,
            'niveau'     => $request->niveau,
        ]);

        return redirect()->route('admin.studentsIndex')
            ->with('success', 'Étudiant mis à jour avec succès ✅');
    }
}
