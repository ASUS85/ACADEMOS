<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Filiere;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Nettoyage pour éviter les doublons
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Filiere::truncate();
        Department::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Département Informatique et Technologies
        $tech = Department::create(['name' => 'Département Informatique et Technologies']);
        Filiere::insert([
            ['name' => 'Génie Logiciel', 'department_id' => $tech->id],
            ['name' => 'Réseaux & Sécurité', 'department_id' => $tech->id],
            ['name' => 'Intelligence Artificielle', 'department_id' => $tech->id],
            ['name' => 'Systèmes Embarqués', 'department_id' => $tech->id],
            ['name' => 'Maintenance Informatique', 'department_id' => $tech->id],
        ]);

        // 2. Département Gestion et Sciences Économiques
        $gestion = Department::create(['name' => 'Département Gestion et Sciences Économiques']);
        Filiere::insert([
            ['name' => 'Comptabilité & Finance', 'department_id' => $gestion->id],
            ['name' => 'Marketing & Commerce', 'department_id' => $gestion->id],
            ['name' => 'Gestion des Ressources Humaines', 'department_id' => $gestion->id],
            ['name' => 'Banque et Microfinance', 'department_id' => $gestion->id],
            ['name' => 'Logistique et Transport', 'department_id' => $gestion->id],
        ]);

        // 3. Département Sciences Juridiques et Administratives
        $droit = Department::create(['name' => 'Sciences Juridiques et Administratives']);
        Filiere::insert([
            ['name' => 'Droit des Affaires', 'department_id' => $droit->id],
            ['name' => 'Administration Publique', 'department_id' => $droit->id],
            ['name' => 'Droit Privé Français', 'department_id' => $droit->id],
            ['name' => 'Sciences Politiques', 'department_id' => $droit->id],
            ['name' => 'Relations Internationales', 'department_id' => $droit->id],
        ]);
    }
}
