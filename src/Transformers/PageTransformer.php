<?php

namespace App\Transformers;

use App\Models\Page;
use Illuminate\Support\Facades\App;
use League\Fractal\TransformerAbstract;

class PageTransformer extends TransformerAbstract
{
    /**
     * @param Page $page
     * @return array
     */
    public function transform(Page $page)
    {
        return [
            'ref'           => $page->ref,
            'menu'          => $page->menu_id,
            'submenu'       => $page->submenu(Page::all()),
            'seo_custom'    => $page->seo_id,
            'seo'           => $page->seo_id,
            'seo_ob'        => $page->seo,
            'blocks'        => $page->blocks,
            'menu_title'    => $page->menu_title,
            'locale'        => App::getLocale(),
            'page_title'    => $page->page_title,
            'block_types'   => $page->block_types,
        ];
    }

    /**
     * @param array $validatedData
     * @return array
     */
    public function conform(array $validatedData)
    {
        return [
            'ref'           => $validatedData['ref'],
            'menu_id'       => $validatedData['menu'],
            'submenu'       => $validatedData['submenu'],
            'seo_id'        => isset($validatedData['seo_custom']) ? $validatedData['seo_custom'] : null,
            'seo_id'        => $validatedData['seo'],
            'seo'           => $validatedData['seo_ob'],
            'blocks'        => $validatedData['blocks'],
            'menu_title'    => $validatedData['menu_title'],
            'locale'        => $validatedData['locale'],
            'page_title'    => $validatedData['page_title'],
            'block_types'   => $validatedData['block_types'],
        ];
    }
}