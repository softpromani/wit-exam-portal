<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Authenticatable
{
    use HasFactory;
    protected $guarded=[];

    function profile_pic(){
        return $this->morphOne(Media::class,'mediable')->where('type','photo');
    }
    function sign(){
        return $this->morphOne(Media::class,'mediable')->where('type','sign');
    }

    public function examForms(){
        return $this->hasMany(ExamForm::class,'student_id');
    }
    public static function checkThisSemFormStatus(){
        $res=ExamForm::where('student_id',auth()->guard('student')->id())
        ->where('semester_id',auth()->guard('student')->user()->semester_id)
        ->whereNot('exam_status','cancel')
        ->exists();
        return $res;
    }
    public function semester(){
        return $this->belongsTo(Semester::class);
    }
    public function course(){
        return $this->belongsTo(Course::class);
    }
    public function branch(){
        return $this->belongsTo(Branch::class);
    }
    public function admission_session(){
        return $this->belongsTo(AdmissionSession::class);
    }
}
