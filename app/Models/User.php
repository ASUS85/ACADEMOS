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

    // Enseignant/Juré : jurys auxquels l'utilisateur appartient.
    public function juries()
    {
        return $this->belongsToMany(Jury::class, 'jury_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    // Enseignant/Juré : rapports où je suis membre du jury.
    public function juryReports()
    {
        return Report::whereHas('juryGroup.members', function ($query) {
            $query->where('users.id', $this->id);
        });
    }

    // Président du jury uniquement.
    public function juryPresidentReports()
    {
        return Report::whereHas('juryGroup.members', function ($query) {
            $query->where('users.id', $this->id)
                ->where('jury_user.role', 'president');
        });
    }

    // Ancienne relation jury_id (legacy, à garder pour compatibilité)
    public function legacyJuryReports()
    {
        return $this->hasMany(Report::class, 'jury_id');
    }
}
