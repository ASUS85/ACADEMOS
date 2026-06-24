<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matiere extends Model
{
    protected $fillable = ['name', 'code', 'filiere_id'];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'matiere_teacher');
    }
}
