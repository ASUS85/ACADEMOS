<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'matricule',
        'grade',
        'department_id',
        'filiere_id',
        'specialite',
        'sexe',
        'role_name',
        'niveau',
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

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'filiere_id');
    }

    public function filieres()
    {
        return $this->belongsToMany(Filiere::class, 'teacher_filiere');
    }

    // Étudiant : rapports soumis
    public function reports()
    {
        return $this->hasMany(Report::class, 'student_id');
    }

    // ⭐ Enseignant : rapports encadrés
    public function assignedReports()
    {
        return $this->hasMany(Report::class, 'teacher_id');
    }

    // ⭐ NOUVEAU : Enseignant/Juré : rapports où je suis juré (table pivot)
    public function juryReports()
    {
        return $this->belongsToMany(Report::class, 'jury_report')
            ->withPivot('is_president')
            ->withTimestamps()
            ->orderBy('pivot_is_president', 'desc');
    }

    // ⭐ NOUVEAU : Président du jury uniquement
    public function juryPresidentReports()
    {
        return $this->belongsToMany(Report::class, 'jury_report')
            ->wherePivot('is_president', true)
            ->withTimestamps();
    }

    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'matiere_teacher');
    }

    // Ancienne relation jury_id (legacy, à garder pour compatibilité)
    public function legacyJuryReports()
    {
        return $this->hasMany(Report::class, 'jury_id');
    }
}
