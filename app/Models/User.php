<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'matricule',
        'grade',
        'specialite'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
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

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function filieres()
    {
        return $this->belongsToMany(Filiere::class, 'teacher_filiere');
    }
}
