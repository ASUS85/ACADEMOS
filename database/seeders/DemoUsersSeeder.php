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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $password = Hash::make('password123');

        // 1. UNIQUE SUPER ADMIN
        $superAdmin = User::create([
            'name'      => 'Super Admin',
            'email'     => 'superadmin@ispcb.cm',
            'password'  => $password,
            'sexe'      => 'M',
            'matricule' => 'MAT-SUPERADMIN-001',
            'role_name' => 'superadmin', // On écrit dans ta colonne
            'grade'     => 'Docteur', // Spécifique aux enseignants, mais on peut laisser vide ou mettre une valeur par défaut
        ]);
        $superAdmin->assignRole('superadmin');

        $departments = Department::all();

        foreach ($departments as $dept) {

            // 2. ADMINS
            for ($i = 1; $i <= 3; $i++) {
                $admin = User::create([
                    'name'          => "Admin $i " . $dept->name,
                    'email'         => "admin{$i}.dept{$dept->id}@ispcb.cm",
                    'password'      => $password,
                    'department_id' => $dept->id,
                    'sexe'          => $i % 2 == 0 ? 'F' : 'M',
                    'matricule' => 'ADM-' . $dept->id . '-' . $i,
                    'role_name'     => 'admin', 
                    'grade'         => 'PLEG', 
                    'specialite'    => 'Administration',
                ]);
                $admin->assignRole('admin');
            }

            // 3. TEACHERS
            for ($i = 1; $i <= 3; $i++) {
                $teacher = User::create([
                    'name'          => "Enseignant $i " . $dept->name,
                    'email'         => "teacher{$i}.dept{$dept->id}@ispcb.cm",
                    'password'      => $password,
                    'department_id' => $dept->id,
                    'sexe'          => $i % 2 == 0 ? 'F' : 'M', 
                    'matricule'     => "MAT-TEACHER-" . $dept->id . '-' . $i . uniqid(),
                    'role_name'     => 'teacher', 
                    'grade'         => ['PLEG', 'MAGE', 'DOCT'][($i - 1) % 3], 
                    'specialite'    => ['Informatique', 'Mathématiques', 'Physique'][($i - 1) % 3], 
                ]);
                $teacher->assignRole('teacher');
            }

            // 4. STUDENTS
            $filieres = Filiere::where('department_id', $dept->id)->get();
            foreach ($filieres as $filiere) {
                for ($i = 1; $i <= 3; $i++) {
                    $student = User::create([
                        'name'          => "Etudiant $i " . $filiere->name,
                        'email'         => "student{$i}.filiere{$filiere->id}@ispcb.cm",
                        'password'      => $password,
                        'niveau'        => "$i",
                        'department_id' => $dept->id,
                        'filiere_id'    => $filiere->id,
                        'sexe'          => $i % 2 == 0 ? 'F' : 'M', 
                        'matricule'  => "STU-{$filiere->id}-{$i}-" . uniqid(),
                        'role_name'     => 'student', 
                    ]);
                    $student->assignRole('student');
                }
            }
        }
    }
}
