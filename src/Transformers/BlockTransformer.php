<?php

namespace App\Transformers;

use App\Models\Block;
use League\Fractal\TransformerAbstract;

class BlockTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Block $block)
    {
        return [
            'ref'       => $block->id,
            'type'      => $block->type,
            'name'      => $block->title,
            'desc'      => $block->body,
            'file'      => $block->media_id,
            'style'     => $block->css,
            'file_slug' => $block->file_slug,
            'page'      => $block->page_id,
            'images'    => $block->album,
        ];
    }

    /**
     * @param array $validatedData
     * @return array
     */
    public function conform(array $validatedData)
    {
        return [
            'id'         => $validatedData['ref'],
            'type'       => $validatedData['type'],
            'title'      => $validatedData['name'],
            'body'       =>  $validatedData['desc'],
            'media_id'   => $validatedData['file'],
            'css'        => $validatedData['style'],
            'file_slug'  => $validatedData['file_slug'],
            'page_id'    => $validatedData['page'],
        ];
    }
}