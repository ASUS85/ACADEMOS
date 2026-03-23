<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jury extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'department_id'
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'jury_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
