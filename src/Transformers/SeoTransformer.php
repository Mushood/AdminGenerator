<?php

namespace App\Transformers;

use App\Models\Seo;
use League\Fractal\TransformerAbstract;

class SeoTransformer extends TransformerAbstract
{
    /**
     * @param Seo $seo
     * @return array
     */
    public function transform(Seo $seo)
    {
        return [
            'viewport'          => $seo->viewport,
            'no_index'          => $seo->no_index,
            'fb_sitename'       => $seo->og_sitename,
            'fb_type'           => $seo->og_type,
            'fb_image'          => $seo->og_image,
            'fb_url'            => $seo->og_url,
            'seo_title'         => $seo->title,
            'seo_description'   => $seo->description,
            'fb_title'          => $seo->og_title,
            'fb_description'    => $seo->og_description,
        ];
    }

    /**
     * @param array $validatedData
     * @return array
     */
    public function conform(array $validatedData)
    {
        return [
            'viewport'          => isset($validatedData['viewport']) ? $validatedData['viewport'] : 'width=device-width, initial-scale=1',
            'no_index'          => isset($validatedData['no_index']) ? $validatedData['no_index'] : false,
            'og_sitename'       => isset($validatedData['fb_sitename']) ? $validatedData['fb_sitename'] : null,
            'og_type'           => isset($validatedData['fb_type']) ? $validatedData['fb_type'] : null,
            'og_image'          => isset($validatedData['fb_image']) ? $validatedData['fb_image'] : null,
            'og_url'            => isset($validatedData['fb_url']) ? $validatedData['fb_url'] : null,
            'title'             => isset($validatedData['seo_title']) ? $validatedData['seo_title'] : null,
            'description'       => isset($validatedData['seo_description']) ? $validatedData['seo_description'] : null,
            'og_title'          => isset($validatedData['fb_title']) ? $validatedData['fb_title'] : null,
            'og_description'    => isset($validatedData['fb_description']) ? $validatedData['fb_description'] : null,
        ];
    }
}