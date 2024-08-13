<?php

namespace App\Models;

se Illuminate\Database\Eloquent\SoftDeletes;use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
u
use Illuminate\Http\UploadedFile;
class Media extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded=['id','created_at','updated_at'];
    /**
     * Get the owning mediable model.
     */
    public function mediable()
    {
        return $this->morphTo();
    }
     /**
     * Upload media file and create a new media record.
     *
     * @param UploadedFile $file
     * @param Model $mediable
     * @return static
     */
    public static function uploadMedia(UploadedFile $file, Model $mediable,String $type=null)
    {
        $filePath = $file->store(class_basename($mediable), 'public');
        
        return self::create([
            'media' => $filePath,
            'size' => $file->getSize()/1024,
            'extension'=>$file->extension(),
            'mediable_id' => $mediable->id,
            'mediable_type' => get_class($mediable),
            'type'=>$type??NULL
        ]);
    }
}
