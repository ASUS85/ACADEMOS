<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public function user()
{
    return $this->belongsTo(User::class);
}

protected $fillable = [
    'report_id',
    'user_id',
    'comment',
    'file_path'
];

public function report()
{
    return $this->belongsTo(Report::class);
}

}
