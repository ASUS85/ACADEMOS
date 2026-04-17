<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matiere;
use App\Models\Filiere;

class MatiereSeeder extends Seeder
{
    public function run(): void
    {
        // Nettoyage uniquement des matières
        Matiere::truncate();

        // Récupérer toutes les filières existantes
        $filieres = Filiere::all();

        foreach ($filieres as $filiere) {
            switch ($filiere->name) {
                // Département Informatique
                case 'Génie Logiciel':
                    Matiere::insert([
                        ['name' => 'Programmation orientée objet', 'code' => 'POO-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Bases de données', 'code' => 'DB-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Ingénierie logicielle', 'code' => 'GL-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Projet tutoré', 'code' => 'PROJ-' . $filiere->id, 'filiere_id' => $filiere->id],
                    ]);
                    break;

                case 'Réseaux & Sécurité':
                    Matiere::insert([
                        ['name' => 'Architecture des réseaux', 'code' => 'NET-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Sécurité informatique', 'code' => 'SEC-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Administration systèmes', 'code' => 'SYS-' . $filiere->id, 'filiere_id' => $filiere->id],
                    ]);
                    break;

                case 'Intelligence Artificielle':
                    Matiere::insert([
                        ['name' => 'Machine Learning', 'code' => 'ML-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Deep Learning', 'code' => 'DL-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Traitement du langage naturel', 'code' => 'NLP-' . $filiere->id, 'filiere_id' => $filiere->id],
                    ]);
                    break;

                case 'Systèmes Embarqués':
                    Matiere::insert([
                        ['name' => 'Électronique numérique', 'code' => 'ELEC-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Programmation bas niveau', 'code' => 'ASM-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'IoT et capteurs', 'code' => 'IOT-' . $filiere->id, 'filiere_id' => $filiere->id],
                    ]);
                    break;

                case 'Maintenance Informatique':
                    Matiere::insert([
                        ['name' => 'Diagnostic matériel', 'code' => 'HARD-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Maintenance logicielle', 'code' => 'SOFT-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Support technique', 'code' => 'SUP-' . $filiere->id, 'filiere_id' => $filiere->id],
                    ]);
                    break;

                // Département Gestion
                case 'Comptabilité & Finance':
                    Matiere::insert([
                        ['name' => 'Comptabilité générale', 'code' => 'COMPTA-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Analyse financière', 'code' => 'FIN-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Fiscalité', 'code' => 'FISC-' . $filiere->id, 'filiere_id' => $filiere->id],
                    ]);
                    break;

                case 'Marketing & Commerce':
                    Matiere::insert([
                        ['name' => 'Marketing stratégique', 'code' => 'MARK-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Études de marché', 'code' => 'ETUDE-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'E-commerce', 'code' => 'ECO-' . $filiere->id, 'filiere_id' => $filiere->id],
                    ]);
                    break;

                case 'Gestion des Ressources Humaines':
                    Matiere::insert([
                        ['name' => 'Droit du travail', 'code' => 'DRTTRAV-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Psychologie du travail', 'code' => 'PSY-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Gestion des carrières', 'code' => 'CARR-' . $filiere->id, 'filiere_id' => $filiere->id],
                    ]);
                    break;

                // Département Droit
                case 'Droit des Affaires':
                    Matiere::insert([
                        ['name' => 'Droit commercial', 'code' => 'DRTCOM-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Droit fiscal', 'code' => 'DRTFISC-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Contrats commerciaux', 'code' => 'CONTR-' . $filiere->id, 'filiere_id' => $filiere->id],
                    ]);
                    break;

                case 'Relations Internationales':
                    Matiere::insert([
                        ['name' => 'Relations diplomatiques', 'code' => 'DIPLO-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Organisations internationales', 'code' => 'ORGINT-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Géopolitique', 'code' => 'GEO-' . $filiere->id, 'filiere_id' => $filiere->id],
                    ]);
                    break;

                default:
                    // Matières génériques pour les autres filières
                    Matiere::insert([
                        ['name' => 'Méthodologie de recherche', 'code' => 'METH-' . $filiere->id, 'filiere_id' => $filiere->id],
                        ['name' => 'Projet tutoré', 'code' => 'PROJ-' . $filiere->id, 'filiere_id' => $filiere->id],
                    ]);
            }
        }
    }
}
