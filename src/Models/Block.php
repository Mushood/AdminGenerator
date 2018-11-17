<?php

namespace App\Models;

use App\Interfaces\Mappable;
use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable;

class Block extends Model implements Mappable
{
    use Translatable;

    const BLOCK_TYPES = [
        'text'      => 'TEXT',
        'editor'    => 'EDITOR',
        'string'    => 'STRING',
        'media'     => 'MEDIA',
        'album'     => 'ALBUM'
    ];

    const VALIDATION = [
        'block.type'   => 'required',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['media_id', 'css', 'type'];

    /**
     * The attributes that are translated.
     *
     * @var array
     */
    public $translatedAttributes = ['title', 'body'];

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
            'ref'       => 'id',
            'type'      => 'type',
            'name'      => 'title',
            'desc'      => 'body',
            'file'      => 'media_id',
            'style'     => 'css',
            'file_slug' => 'file_slug',
            'page'      => 'page_id',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function media()
    {
        if ($this->media_id == null)
            return null;

        return $this->belongsTo('App\Models\Media');
    }
}
