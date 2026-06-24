<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    const STATUS_SUBMITTED = 'Soumis';
    const STATUS_ASSIGNED = 'Affecté';
    const STATUS_COMMENTED = 'commenté';
    const STATUS_JURY_PENDING = 'En attente jury';

    const STATUS_PENDING = 'En attente';
    const STATUS_VALIDATED = 'Validé';
    const STATUS_FINAL = 'Validé final';
    const STATUS_REJECTED = 'Rejeté';

    protected $fillable = [
        'student_id',
        'title',
        'file_path',
        'status',
        'teacher_id',
        'jury_id', // ← On garde pour compatibilité/mapping président
        'teacher_comment',
        'teacher_status',
        'jury_note_forme',
        'jury_note_fond',
        'jury_note_langage',
        'jury_moyenne_finale',
        'jury_appreciation',
        'jury_commentaire',
        'jury_decision'
    ];

    // Relation : Rapport ← Étudiant
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relation : Rapport ← Enseignant affecté (encadreur)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Relation : Rapport ← Président du jury (champ legacy)
    public function jury()
    {
        return $this->hasOne(Jury::class);
    }
    public function versions()
    {
        return $this->hasMany(ReportVersion::class)->orderBy('created_at', 'desc');
    }

    public function getJuryPresidentAttribute()
    {
        $jury = $this->relationLoaded('juryGroup')
            ? $this->juryGroup
            : $this->juryGroup()->with('members')->first();

        return $jury?->members->firstWhere('pivot.role', 'president');
    }

    public function latestVersion()
    {
        return $this->hasOne(ReportVersion::class)->latest('created_at');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function juryGroup()
    {
        return $this->hasOne(Jury::class);
    }
}
