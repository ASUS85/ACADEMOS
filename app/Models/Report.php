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
        return $this->belongsTo(User::class, 'jury_id');
    }

    // ⭐ NOUVEAU : Tous les membres du jury (1-4)
    // Dans app/Models/Report.php
    public function juryMembers()
    {
        return $this->belongsToMany(User::class, 'report_jury', 'report_id', 'user_id')
            ->withPivot('is_president')
            ->withTimestamps()
            ->orderBy('pivot_is_president', 'desc');
    }


    // Président du jury via table pivot (recommandé)
    public function juryPresidentRelation()
    {
        return $this->belongsToMany(User::class, 'jury_report')
            ->wherePivot('is_president', true)
            ->withTimestamps();
    }

    public function versions()
    {
        return $this->hasMany(ReportVersion::class)->orderBy('created_at', 'desc');
    }

    public function getJuryPresidentAttribute()
    {
        return $this->juryMembers()->wherePivot('is_president', true)->first();
    }

    public function latestVersion()
    {
        return $this->hasOne(ReportVersion::class)->latest('created_at');
    }
}
