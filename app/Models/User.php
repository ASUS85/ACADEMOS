<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // Bien présent !

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles; // On regroupe les traits ici

    /**
     * Les attributs assignables en masse.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'matricule',
        'grade',
        'department_id',
        'filiere_id',    // Ajouté : nécessaire pour tes étudiants
        'specialite',    // Utilisé pour les enseignants ou comme libellé
        'sexe',
        'role_name',     // Ajouté : pour stocker le nom du rôle en clair
        'niveau',        // Ajouté : utile pour distinguer Niveau 1, 2, 3, etc.
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELATIONS ---

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relation vers la filière (pour les étudiants)
     */
    public function filiere()
    {
        // On utilise 'filiere_id' qui est la clé étrangère standard
        return $this->belongsTo(Filiere::class, 'filiere_id');
    }

    /**
     * Pour les enseignants qui gèrent plusieurs filières
     */
    public function filieres()
    {
        return $this->belongsToMany(Filiere::class, 'teacher_filiere');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'student_id');
    }

    public function assignedReports()
    {
        return $this->hasMany(Report::class, 'teacher_id');
    }

    public function juryReports()
    {
        return $this->hasMany(Report::class, 'jury_id');
    }
}
