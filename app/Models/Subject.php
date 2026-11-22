<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_code',
        'title',
        'course_id',
        'branch_id',
        'semester_id',
        'type',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Human-friendly type label (t -> Theory, p -> Practical, etc.)
     */
    public function getTypeNameAttribute(): string
    {
        $val = strtolower(trim((string) $this->type));
        if ($val === 't' || $val === 'theory') {
            return 'Theory';
        }
        if ($val === 'p' || $val === 'practical') {
            return 'Practical';
        }
        return $this->type ? ucfirst($this->type) : 'N/A';
    }
}
