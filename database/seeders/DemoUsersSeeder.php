<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // SUPER ADMIN
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@ispcb.cm',
            'password' => Hash::make('password123'),
        ]);
        $superAdmin->assignRole('superadmin');



        // ADMINS
        $admin1 = User::create([
            'name' => 'Admin 1',
            'email' => 'admin1@ispcb.cm',
            'password' => Hash::make('password123'),
        ]);
        $admin1->assignRole('admin');



        $admin2 = User::create([
            'name' => 'Admin 2',
            'email' => 'admin2@ispcb.cm',
            'password' => Hash::make('password123'),
        ]);
        $admin2->assignRole('admin');



        // TEACHERS
        $teacher1 = User::create([
            'name' => 'Teacher 1',
            'email' => 'teacher1@ispcb.cm',
            'password' => Hash::make('password123'),
        ]);
        $teacher1->assignRole('teacher');



        $teacher2 = User::create([
            'name' => 'Teacher 2',
            'email' => 'teacher2@ispcb.cm',
            'password' => Hash::make('password123'),
        ]);
        $teacher2->assignRole('teacher');



        // STUDENTS
        for ($i = 1; $i <= 2; $i++) {
            $student = User::create([
                'name' => "Student $i",
                'email' => "student$i@ispcb.cm",
                'password' => Hash::make('password123'),
            ]);

            $student->assignRole('student');
        }
    }
}
