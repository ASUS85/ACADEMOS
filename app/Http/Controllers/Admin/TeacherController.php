<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Filiere;

class TeacherController extends Controller
{
    public function create()
    {
        $department = auth()->user()->department;

        $specialites = Filiere::where('department_id', $department->id)->get();

        return view('admin.teachers.create', compact('department','specialites'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'matricule' => 'required|unique:users',
            'grade' => 'required',
            'specialite' => 'required|exists:filieres,id',
            'sexe' => 'required'
        ]);

        $teacher = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'matricule' => $request->matricule,
            'grade' => $request->grade,
            'specialite' => (int)$request->specialite,
            'sexe' => $request->sexe,
            'department_id' => auth()->user()->department_id,
            'password' => bcrypt('password123'),
            'created_by' => auth()->id()
        ]);

        $teacher->assignRole('teacher');

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Enseignant créé avec succès');
    }


    public function index()
    {
        $departmentId = auth()->user()->department_id;

        $users = User::role('teacher')
            ->where('department_id', $departmentId)
            ->with('filiere')
            ->paginate(10);

        return view('admin.teachers.index', compact('users'));
    }


    public function update(Request $request, User $teacher)
    {
        if (
            $teacher->department_id != auth()->user()->department_id &&
            $teacher->created_by != auth()->id()
        ) {
            abort(403);
        }

        $teacher->update($request->only([
            'name',
            'email',
            'matricule',
            'grade',
            'specialite',
            'sexe'
        ]));

        return back()->with('success', 'Mis à jour');
    }


    public function destroy(User $teacher)
    {
        if (
            $teacher->department_id != auth()->user()->department_id &&
            $teacher->created_by != auth()->id()
        ) {
            abort(403);
        }

        $teacher->delete();

        return back()->with('success', 'Supprimé');
    }
}
