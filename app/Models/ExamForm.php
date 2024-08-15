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

    public function payment(){
        return $this->MorphOne(Payment::class,'paymentable');
    }


}
