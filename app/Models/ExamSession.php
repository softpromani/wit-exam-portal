<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function exam_session_has_cbs()
    {
        return $this->hasMany(ExamSessionHasCBS::class);
    }
}
