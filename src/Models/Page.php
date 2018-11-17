<?php

namespace App\Models;

use App\Interfaces\Mappable;
use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Page extends Model implements Mappable
{
    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ref','menu_id', 'seo_id'];

    /**
     * The attributes that are translated.
     *
     * @var array
     */
    public $translatedAttributes = ['menu_title', 'page_title'];

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
            'ref'           => 'ref',
            'menu'          => 'menu_id',
            'submenu'       => 'submenu',
            'seo_custom'    => 'seo_id',
            'seo'           => 'seo_id',
            'seo_ob'        => 'seo',
            'blocks'        => 'blocks',
            'menu_title'    => 'menu_title',
            'locale'        => 'locale',
            'page_title'    => 'page_title',
            'block_types'   => 'block_types',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function media()
    {
        return $this->belongsTo('App\Models\Media');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'ref';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    /**
     * @param $pages
     * @return string
     */
    public function submenu($pages)
    {
        if ($this->menu_id == null) {
            return "-";
        }

        if ($this->menu_id == 0) {
            return "-";
        }

        if ($this->menu_id > 0) {
            foreach ($pages as $page) {
                if($page->id == $this->menu_id)
                    return $page->ref;
            }
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function seo()
    {
        return $this->belongsTo('App\Models\Seo');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blocks()
    {
        return $this->hasMany('App\Models\Block');
    }

    /**
     * @param $pages
     * @param null $locale
     * @return array
     * @throws \Exception
     */
    public static function getNavigation($pages, $locale = null)
    {
        $navs = [];

        foreach ($pages as $page) {
            if ($page->menu_id === null) {
                continue;
            }

            if ($page->menu_id === 0) {
                $route = self::getRouteFromRef($page->ref, $locale);
                $navs[$page->id] =  [
                    'title' => $page->menu_title,
                    'route' => $route['name'],
                    'anchor' =>$route['anchor'],
                    'submenu' => []
                ];
            }
        }

        foreach ($pages as $key => $page) {
            if ($page->menu_id > 0) {
                foreach ($navs as $keyNav => $nav) {
                    if($keyNav == $page->menu_id) {
                        $route = self::getRouteFromRef($page->ref, $locale);
                        $navs[$keyNav]['submenu'][$page->id] =  [
                            'title' => $page->menu_title,
                            'route' => $route['name'],
                            'anchor' =>$route['anchor'],
                        ];
                    }
                }
            }
        }

        return $navs;
    }

    /**
     * @param $ref
     * @param $locale
     * @return array
     * @throws \Exception
     */
    private static function getRouteFromRef($ref, $locale)
    {
        $route = explode("#",$ref);

        if (count($route) == 1) {
            return [
                'name' => $route[0] . ($locale != null ? "_locale" : ""),
                'anchor' => ''
            ];
        }

        if (count($route) == 2) {
            return [
                'name' => $route[0] . ($locale != null ? "_locale" : ""),
                'anchor' => '#' . $route[1]
            ];
        }

        throw new \Exception("Error Processing Request", 1);

    }
}
