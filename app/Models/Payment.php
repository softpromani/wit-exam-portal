<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Payment extends Model
{
    use HasFactory,SoftDeletes;

    public function paymentable()
    {
        return $this->morphTo();
    }

     /**
     * Upload media file and create a new media record.
     *
     * @param Model $mediable
     * @return static
     */
    public static function uploadPayment(Model $mediable)
    {
    
        return self::create([
          
        ]);
    }
}
