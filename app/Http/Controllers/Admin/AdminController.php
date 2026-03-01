<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

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
        $roles = ['superadmin', 'admin', 'teacher', 'student', 'jury'];

        return view('admin.superadmin.edit-user', compact('user', 'departments', 'roles'));
    }

    // Mettre à jour un utilisateur
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'nullable|string', // pour changer le rôle
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
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

        return redirect()->route('superadmin.users')
            ->with('success', 'Utilisateur supprimé avec succès ❌');
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
    public function storeAdmin(Request $request)
    {
        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'department_id' => $request->department_id
        ]);
        $admin->assignRole('admin');
    }

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
}
