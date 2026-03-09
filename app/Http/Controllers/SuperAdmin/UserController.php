<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Filiere;


class UserController extends Controller
{
    public function index(Request $request)
    {

        $users = User::query();

        // filtre role
        if ($request->role) {
            $users->role($request->role);
        }

        // filtre département
        if ($request->department_id) {
            $users->where('department_id', $request->department_id);
        }

        // filtre spécialité (texte)
        if ($request->specialite) {
            $users->where('specialite', $request->specialite);
        }

        $users = $users->with(['roles', 'department'])->paginate(10);

        $departments = Department::all();
        $filieres = Filiere::all();

        return view('superadmin.users.index', compact(
            'users',
            'departments',
            'filieres'
        ));
    }
}
