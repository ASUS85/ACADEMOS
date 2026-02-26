<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\filieres;

class Department extends Model
{
    protected $fillable = ['name'];

    public function filieres()
    {
        return $this->hasMany(Filiere::class);
    }

    public function admins()
    {
        return $this->hasMany(User::class);
    }
}
