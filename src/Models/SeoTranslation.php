<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoTranslation extends Model
{
    /**
     * Disable timestamps on translation
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'og_title', 'og_description'];

    public function seo()
    {
        return $this->belongsTo('App\Models\Seo');
    }
}
