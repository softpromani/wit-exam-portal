<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSchedule extends Model
{
    use HasFactory,SoftDeletes;
    protected $guarded=[];

    public function exam_session(){
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }
    public function subject(){
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
