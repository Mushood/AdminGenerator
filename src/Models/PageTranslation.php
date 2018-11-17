<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
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
    protected $fillable = ['menu_title', 'page_title'];

    public function page()
    {
        return $this->belongsTo('App\Models\Page');
    }
}
