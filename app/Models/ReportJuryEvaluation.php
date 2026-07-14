<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportJuryEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'technical_note',
        'presentation_note',
        'content_note',
        'final_score',
        'decision',
        'comment',
    ];

    protected $casts = [
        'technical_note' => 'decimal:2',
        'presentation_note' => 'decimal:2',
        'content_note' => 'decimal:2',
        'final_score' => 'decimal:2',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
