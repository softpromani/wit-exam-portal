<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class ExamForm extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id', 'semester_id', 'session_id', 'result_status', 'exam_status',
    ];

    protected $casts = [
        'subject_ids' => 'array', // Assuming subject_ids is sent as an array
    ];

   
}
