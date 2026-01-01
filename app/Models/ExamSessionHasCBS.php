<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSessionHasCBS extends Model
{
    use HasFactory;
    protected $table = 'exam_session_has_cbs';
    protected $guarded = [];

    protected $casts = [
        'semesters' => 'array',
        'amounts' => 'array',
        'semester_amounts' => 'array'
    ];

    public function exam_session()
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
