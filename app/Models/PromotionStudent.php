<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'student_id',
        'from_semester_id',
        'to_semester_id',
        'status',
        'reverted_at',
    ];

    protected $casts = [
        'reverted_at' => 'datetime',
    ];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function fromSemester()
    {
        return $this->belongsTo(Semester::class, 'from_semester_id');
    }

    public function toSemester()
    {
        return $this->belongsTo(Semester::class, 'to_semester_id');
    }
}
