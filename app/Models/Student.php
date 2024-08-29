<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Authenticatable
{
    use HasFactory;
    protected $fillable=['university_roll_no','registration_no','registration_type','student_name','course_id','branch_id',
                        'semester_id','admission_session_id','password','gender','email','mobile_number','fname','mname','parent_number',
                        'address','is_profile','dob','adhar_number'
                        ];

    function profile_pic(){
        return $this->morphOne(Media::class,'mediable')->where('type','photo');
    }
    function sign(){
        return $this->morphOne(Media::class,'mediable')->where('type','sign');
    }

    public function examForms(){
        return $this->hasMany(ExamForm::class,'student_id');
    }
    public static function checkThisSemFormStatus($exam_session_id){
        $res=ExamForm::where('student_id',auth()->guard('student')->id())
        ->where('semester_id',auth()->guard('student')->user()->semester_id)
        ->where('session_id',$exam_session_id)
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
    public function exam_session()
        {
            return $this->belongsTo(ExamSession::class, 'admission_session_id', 'id');
        }

}
