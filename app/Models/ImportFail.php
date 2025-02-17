<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportFail extends Model
{
    use HasFactory;
    protected $fillable = ['file_name', 'row_data','reason'];

    protected $casts = [
        'row_data' => 'array',
    ];
}
