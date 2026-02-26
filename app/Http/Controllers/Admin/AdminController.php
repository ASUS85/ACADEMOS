<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Report;


class AdminController extends Controller
{
    public function index()
    {
        $admins = User::role('admin')->with('roles')->paginate(10);
        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('superAdmin')) abort(403);
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('superAdmin')) abort(403);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $admin = User::create($request->only('name', 'email', 'password'));
        $admin->assignRole('admin');

        return redirect()->route('admin.admins.index')->with('success', '✅ Admin créé !');
    }

    public function superadminUsers()
    {
        $users = User::with('roles')->paginate(20);
        return view('admin.superadmin.users', compact('users'));
    }

    public function superadminReports()
    {
        $reports = Report::with(['student', 'latestVersion'])->latest()->paginate(25);
        $stats = [
            'total' => Report::count(),
            'pending' => Report::where('status', '!=', 'Validé final')->count(),
        ];
        return view('admin.superadmin.reports', compact('reports', 'stats'));
    }

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
