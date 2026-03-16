<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Filiere;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoUsersSeeder extends Seeder
{
    public function run()
    {
        // Nettoyage préalable des utilisateurs pour éviter les doublons si fresh n'est pas utilisé
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $password = Hash::make('password123');

        // 1. UNIQUE SUPER ADMIN
        $superAdmin = User::create([
            'name'     => 'Super Admin',
            'email'    => 'superadmin@ispcb.cm',
            'password' => $password,
        ]);
        $superAdmin->assignRole('superadmin');

        // Récupération des départements pour la répartition
        $departments = Department::all();

        foreach ($departments as $dept) {

            // 2. TROIS ADMINS PAR DÉPARTEMENT
            for ($i = 1; $i <= 3; $i++) {
                $admin = User::create([
                    'name'          => "Admin $i " . $dept->name,
                    'email'         => "admin{$i}.dept{$dept->id}@ispcb.cm",
                    'password'      => $password,
                    'department_id' => $dept->id,
                ]);
                $admin->assignRole('admin');
            }

            // 3. TROIS TEACHERS PAR DÉPARTEMENT
            for ($i = 1; $i <= 3; $i++) {
                $teacher = User::create([
                    'name'          => "Enseignant $i " . $dept->name,
                    'email'         => "teacher{$i}.dept{$dept->id}@ispcb.cm",
                    'password'      => $password,
                    'department_id' => $dept->id,
                ]);
                $teacher->assignRole('teacher');
            }

            // 4. TROIS ÉLÈVES PAR FILIÈRE (On boucle sur les filières du département)
            $filieres = Filiere::where('department_id', $dept->id)->get();
            foreach ($filieres as $filiere) {
                for ($i = 1; $i <= 3; $i++) {
                    $student = User::create([
                        'name'          => "Etudiant $i " . $filiere->name,
                        'email'         => "student{$i}.filiere{$filiere->id}@ispcb.cm",
                        'password'      => $password,
                        'department_id' => $dept->id,
                        'filiere_id'    => $filiere->id,
                        'matricule'     => "MAT-" . strtoupper(substr($filiere->name, 0, 3)) . "-$filiere->id-$i",
                    ]);
                    $student->assignRole('student');
                }
            }
        }
    }
}
