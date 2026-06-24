<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Department;
use App\Models\Filiere;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $departments = Department::all();

       return view('auth.register', compact('departments'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255','unique:users'],
            'matricule' => ['required','string','max:50','unique:users'],
            'department_id' => ['required','exists:departments,id'],
            'filiere_id' => ['required','exists:filieres,id'],
            'password' => ['required','confirmed', Rules\Password::defaults()],
        ]);

        // Récupérer filiere
        $filiere = Filiere::findOrFail($request->filiere_id);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'matricule' => $request->matricule,
            'password' => Hash::make($request->password),
            'department_id' => $request->department_id,
            'filiere_id' => $filiere->id,
            'specialite' => $filiere->id,
            'role_name' => 'student',
        ]);

        //  ASSIGNE RÔLE ÉTUDIANT AUTOMATIQUEMENT
        $user->assignRole('student');

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
