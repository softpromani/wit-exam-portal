<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_no',
        'promoted_by',
        'students_count',
        'from_semester_name',
        'to_semester_name',
        'filter_criteria',
        'status',
        'reverted_at',
        'reverted_by',
        'notes',
    ];

    protected $casts = [
        'filter_criteria' => 'array',
        'reverted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'promoted_by');
    }

    public function revertedByUser()
    {
        return $this->belongsTo(User::class, 'reverted_by');
    }

    public function promotionStudents()
    {
        return $this->hasMany(PromotionStudent::class, 'promotion_id');
    }
}
