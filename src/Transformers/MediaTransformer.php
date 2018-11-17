<?php

namespace App\Transformers;

use App\Models\Media;
use League\Fractal\TransformerAbstract;

class MediaTransformer extends TransformerAbstract
{
    /**
     * @param Media $media
     * @return array
     */
    public function transform(Media $media)
    {
        return [
            'file' => $media->filename,
            'url'  => $media->path,
            'slug' => $media->slug
        ];
    }

    /**
     * @param array $validatedData
     * @return array
     */
    public function conform(array $validatedData)
    {
        return [
            'filename'  => $validatedData['file'],
        ];
    }
}
