<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Filiere;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; //
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TeacherController extends Controller
{
    public function create()
    {
        $department = auth()->user()->department;

        $specialites = Filiere::where('department_id', $department->id)->get();
        $filiere_id = $specialites->first();
        $matieres = $filiere_id ? Matiere::where('filiere_id', $filiere_id->id)->get() : collect();

        return view('admin.teachers.create', compact('department', 'specialites', 'matieres'));
    }


    public function store(Request $request)
    {
        Log::debug('Teacher store payload raw', $request->all());

        try {
            $validated = $request->validate([
                'name'      => ['required', 'string', 'max:255'],
                'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
                'matricule' => ['required', 'string', 'max:100', 'unique:users,matricule'],
                'grade'     => ['required', 'string', 'max:100'],
                'sexe'      => ['required', 'in:Homme,Femme'],
                'filieres'  => ['nullable', 'array'],
                'filieres.*' => ['integer', 'exists:filieres,id'],
                'matieres'  => ['nullable', 'array'],
                'matieres.*' => ['integer', 'exists:matieres,id'],
            ]);

            Log::debug('Validation OK', $validated);

            $teacher = User::create([
                'name'          => $validated['name'],
                'email'         => $validated['email'],
                'matricule'     => $validated['matricule'],
                'grade'         => $validated['grade'],
                'sexe'          => $validated['sexe'],
                'department_id' => auth()->user()->department_id,
                'password'      => Hash::make('password123'),
                'created_by'    => auth()->id(),
            ]);

            Log::debug('User created', ['id' => $teacher->id]);

            $teacher->assignRole('teacher');
            Log::debug('Role assigned');

            $teacher->filieres()->sync($validated['filieres'] ?? []);
            Log::debug('Filieres synced');

            $teacher->matieres()->sync($validated['matieres'] ?? []);
            Log::debug('Matieres synced');

           return back()->with('success', 'Enseignant créé avec succès ');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            Log::error('Store failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            throw $e;
        }
    }



    public function index()
    {
        $departmentId = auth()->user()->department_id;

        $users = User::role('teacher')
            ->where('department_id', $departmentId)
            ->with('filiere')
            ->latest()
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

        // Mise à jour des champs simples
        $teacher->update($request->only([
            'name',
            'email',
            'matricule',
            'grade',
            'sexe'
        ]));

        // Synchroniser les filières (même si vide)
        $teacher->filieres()->sync($request->input('filieres', []));

        // Synchroniser les matières (même si vide)
        $teacher->matieres()->sync($request->input('matieres', []));

        return back()->with('success', 'Enseignant mis à jour avec succès ✅');
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
