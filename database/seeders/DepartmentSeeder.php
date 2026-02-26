<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Filiere;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        $tech = Department::create(['name' => 'Département Informatique et Technologies']);
        Filiere::insert([
            ['name' => 'Génie Logiciel', 'department_id' => $tech->id],
            ['name' => 'Réseaux & Sécurité', 'department_id' => $tech->id],
        ]);

        $gestion = Department::create(['name' => 'Département Gestion et Sciences Économiques']);
        Filiere::insert([
            ['name' => 'Comptabilité & Finance', 'department_id' => $gestion->id],
            ['name' => 'Marketing & Commerce', 'department_id' => $gestion->id],
        ]);

        $droit = Department::create(['name' => 'Sciences Juridiques et Administratives']);
        Filiere::insert([
            ['name' => 'Droit des Affaires', 'department_id' => $droit->id],
            ['name' => 'Administration Publique', 'department_id' => $droit->id],
        ]);
    }
}
