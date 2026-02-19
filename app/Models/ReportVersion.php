<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportVersion extends Model
{
    protected $fillable = ['report_id', 'user_id', 'version', 'file_path', 'comment', 'action'];

    public function report() { return $this->belongsTo(Report::class); }
    public function user() { return $this->belongsTo(User::class); }
}
