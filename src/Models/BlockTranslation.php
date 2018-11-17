<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockTranslation extends Model
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
    protected $fillable = ['title', 'body'];

    public function block()
    {
        return $this->belongsTo('App\Models\Block');
    }
}
