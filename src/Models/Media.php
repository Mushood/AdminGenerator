<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Library\InterventionWrapperImage;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Cviebrock\EloquentSluggable\Sluggable;

class Media extends Model
{
    use Sluggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['filename', 'path'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'filename'
            ]
        ];
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public static function storeFile($file, $folder = null)
    {
        $filename   =  Carbon::now()->timestamp . '_' . ($file->getClientOriginalName());
        $manager    = new ImageManager();
        $savedImage = $manager->make($file->getRealPath());
        $savedImage = new InterventionWrapperImage($savedImage);

        if($folder === null) {
            $folder = (new \ReflectionClass(get_called_class()))->getShortName();
        }

        Storage::disk('public')->putFileAs($folder, $savedImage, $filename);

        return [
            'filename'  => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'path'      => Storage::url($folder . '/' . $filename)
        ];
    }
}
