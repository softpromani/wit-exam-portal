<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ExamForm extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id', 'semester_id', 'session_id', 'result_status', 'exam_status',
    ];

    public function payment()
    {
        return $this->MorphOne(Payment::class,'paymentable');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function exam_session()
    {

        return $this->belongsTo(ExamSession::class,'session_id');

    }

    // Define a many-to-many relationship with Subject
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'exam_form_subjects', 'exam_form_id', 'subject_id');
    }
}
