<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Filiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; //
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function create()
    {
        $department = auth()->user()->department;

        $specialites = Filiere::where('department_id', $department->id)->get();

        return view('admin.teachers.create', compact('department', 'specialites'));
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
            'filiere_id' => (int)$request->specialite,
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

        $data = $request->only(['name', 'email', 'matricule', 'grade', 'specialite', 'sexe']);

        if ($request->filled('specialite')) {
            $data['filiere_id'] = (int) $request->specialite;
        }

        $teacher->update($data);

        return back()->with('success', 'Mis à jour');
    }

    public function edit(User $teacher)
    {
        if (
            $teacher->department_id != auth()->user()->department_id &&
            $teacher->created_by != auth()->id()
        ) {
            abort(403);
        }

        $filieres = Filiere::where('department_id', auth()->user()->department_id)->get();

        return view('admin.teachers.edit', compact('teacher', 'filieres'));
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

    public function showProfile()
    {
        $teacher = auth()->user()->load(['department', 'filiere', 'reports']);
        return view('admin.teachers.profile', compact('teacher'));
    }

    // ✅ UPDATE PROFILE (CORRIGÉ)
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore(auth()->id())
            ],
            'matricule' => 'nullable|string|max:50|unique:users,matricule,' . auth()->id(),
            'specialite' => 'nullable|string|max:100',
        ]);

        auth()->user()->update($request->only(['name', 'email', 'matricule', 'specialite']));

        return back()->with('success', '✅ Profil mis à jour avec succès');
    }

    // ✅ UPDATE PASSWORD
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', '✅ Mot de passe modifié avec succès');
    }
}
