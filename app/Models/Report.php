<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'file_path',
        'status',
        'teacher_id',
        'jury_id',
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

    // Relation : Rapport ← Enseignant affecté
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Relation : Rapport ← Jury évaluateur
    public function jury()
    {
        return $this->belongsTo(User::class, 'jury_id');
    }

    public function versions()
    {
        return $this->hasMany(ReportVersion::class)->orderBy('created_at', 'desc');
    }

    public function latestVersion()
    {
        return $this->hasOne(ReportVersion::class)->latest('created_at');
    }
}
