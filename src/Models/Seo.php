<?php

namespace App\Models;

use App\Interfaces\Mappable;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Seo extends Model implements Mappable
{
    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['viewport', 'no_index', 'og_sitename', 'og_type', 'og_image', 'og_url'];

    /**
     * The attributes that are translated.
     *
     * @var array
     */
    public $translatedAttributes = ['title', 'description', 'og_title', 'og_description'];

    /**
     * @var array
     */
    protected $with = ['translations'];

    /**
     * @return array
     */
    public static function mapper()
    {
        return [
            'viewport'          => 'viewport',
            'no_index'          => 'no_index',
            'fb_sitename'       => 'og_sitename',
            'fb_type'           => 'og_type',
            'fb_image'          => 'og_image',
            'fb_url'            => 'og_url',
            'seo_title'         => 'title',
            'seo_description'   => 'description',
            'fb_title'          => 'og_title',
            'fb_description'    => 'og_description',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function media()
    {
        return $this->belongsTo('App\Models\Media');
    }
}
