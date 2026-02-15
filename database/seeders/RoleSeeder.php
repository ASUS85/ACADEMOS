<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'student']);  // Étudiant
        Role::create(['name' => 'teacher']);  // Enseignant
        Role::create(['name' => 'admin']);    // Admin
        Role::create(['name' => 'jury']);     // Jury
    }
}
