<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\Media;
use App\Models\Seo;
use App\Transformers\BlockTransformer;
use App\Transformers\PageTransformer;
use App\Transformers\SeoTransformer;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class PageController extends Controller
{
    /**
     * @var PageTransformer
     */
    protected $transformer;

    /**
     * PageController constructor.
     * @param PageTransformer $transformer
     */
    public function __construct(PageTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($locale)
    {
        App::setLocale($locale);
        $pages = Page::orderby('created_at', 'desc')->get();

        foreach ($pages as $page) {
            $page->seo      = $page->seo_id;
        }

        $pages = fractal($pages, $this->transformer);

        return response()->json(
            $pages, Response::HTTP_OK
        );
    }

    /**
     * Display the specified resource
     *
     * @param Page $page
     * @param null $locale
     * @param SeoTransformer $seoTransformer
     * @param BlockTransformer $blockTransformer
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Page $page, $locale =  null, SeoTransformer $seoTransformer, BlockTransformer $blockTransformer)
    {
        if ($locale == null) {
            $locale = App::getLocale();
        } else {
            App::setLocale($locale);
        }

        if (!$page->hasTranslation($locale)) {
            $page = fractal($page, $this->transformer);
            $page->addMeta([
                'type' => 'translation'
            ]);

            return response()->json(
                $page, Response::HTTP_OK
            );
        }

        $seo    = $page->seo;
        $blocks = $page->blocks;
        $blocksMapped = [];

        if($seo != null)
            $page->seo = fractal($seo, $seoTransformer)->toArray()['data'];

        if($blocks != null) {
            foreach ($blocks as $block) {
                $blockMapped = fractal($block, $blockTransformer)->toArray()['data'];
                if ($blockMapped['type'] == 'MEDIA') {
                    $media = Media::find($blockMapped['file']);
                    $blockMapped['file'] = $media->path;
                    $blockMapped['file_slug'] = $media->slug;
                }
                array_push($blocksMapped, $blockMapped);
            }
        }

        $page->blocks       = $blocksMapped;
        $page->block_types  = Block::BLOCK_TYPES;

        $page = fractal($page, $this->transformer);

        return response()->json(
            $page, Response::HTTP_OK
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
        $page = fractal($page, $this->transformer);

        return response()->json(
            $page, Response::HTTP_OK
        );
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param Request $request
     * @param Page $page
     * @param SeoTransformer $seoTransformer
     * @param BlockTransformer $blockTransformer
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Page $page, SeoTransformer $seoTransformer, BlockTransformer $blockTransformer)
    {
        $seoType = null;
        if(isset($request->page['seo_custom']))
            $seoType = $request->page['seo_custom'];

        $mappedPage     = $this->transformer->conform($request->page);

        $locale         = App::getLocale();
        if(isset($mappedPage['locale'])){
            $locale         = $mappedPage['locale'];
            App::setLocale($locale);
        }

        if(isset($mappedPage['seo'])) {
            $mappedSeo = $seoTransformer->conform($mappedPage['seo']);

            $this->updateSeo($mappedSeo, $seoType, $page);
            unset($mappedPage['seo']);
            unset($mappedPage['seo_id']);
        }

        if(isset($mappedPage['blocks'])) {
            foreach ($mappedPage['blocks'] as $block) {
                $mappedBlock = $blockTransformer->conform($block);

                if($mappedBlock['type'] == "MEDIA") {
                    $media = Media::where('slug', $mappedBlock['file_slug'])->first();
                    $mappedBlock['media_id'] = $media->id;
                } else {
                    $mappedBlock['media_id'] = null;
                }

                $updatedBlock = Block::find($mappedBlock['id']);
                $updatedBlock->update($mappedBlock);
            }
        }

        $page->update($mappedPage);

        $page           = fractal($page, $this->transformer);

        return response()->json(
            $page, Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PageTranslation  $pageTranslation
     * @return \Illuminate\Http\Response
     */
    public function destroy(PageTranslation $pageTranslation)
    {
        $page = $pageTranslation->page;
        $page->delete();

        return response()->json(
            [], Response::HTTP_NO_CONTENT
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function menu (Request $request)
    {
        $pages = $request->pages;
        $locale = $request->language;

        App::setLocale($locale);

        foreach ($pages as $page) {
            $menu = Page::where('ref', $page['ref'])->first();
            if(isset($page['menu'])) {
                $menu->update([
                    'menu_id' => $page['menu'] == 0 ? $page['menu'] : Page::where('ref', $page['submenu'])->first()->id,
                    'menu_title' => $page['menu_title']
                ]);
            } else {
                $menu->update([
                    'menu_id' => null,
                    'menu_title' => ""
                ]);
            }

        }

        return response()->json(
            null, Response::HTTP_OK
        );
    }

    /**
     * @param array $seo
     * @param $seoType
     * @param Page $page
     * @return bool
     */
    private function updateSeo(array $seo, $seoType, Page $page)
    {
        if ($seoType == null) {
            $delete = 0;

            if ($page->seo != null && $page->seo->id != 1) {
                $delete = $page->seo->id;
            }

            $page->seo_id = null;
            $page->save();

            if($delete) {
                $seoDelete = Seo::find($delete);
                $seoDelete->delete();
            }

            return true;
        }

        if ($seoType == 1) {
            $delete = 0;
            if ($page->seo_id == 1) {

                return true;
            }

            if ($page->seo_id > 1) {
                $delete =  $page->seo_id;
            }

            $page->seo_id = 1;
            $page->save();

            if($delete) {
                $seoDelete = Seo::find($delete);
                $seoDelete->delete();
            }

            return true;
        }

        if ($seoType == 2) {
            if ($page->seo_id == 1 || $page->seo_id == null) {
                $seoNew = Seo::create($seo);
                $page->seo_id = $seoNew->id;
                $page->save();

                return true;
            }

            $page->seo->update($seo);

            return true;
        }

        return false;
    }
}
