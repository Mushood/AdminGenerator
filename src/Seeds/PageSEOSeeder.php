<?php

use Illuminate\Database\Seeder;

class PageSEOSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\App::setLocale('en');
        $seo = \App\Models\Seo::create(
            [
                'viewport'      => 'width=device-width, initial-scale=1',
                'title'         => 'Site Title',
                'description'   => 'Description',
            ]
        );

        $pageHome = \App\Models\Page::create(
            [
                'ref'           => 'home',
                'menu_id'       => 0,
                'seo_id'        => $seo->id,
                'menu_title'    => 'Home',
                'page_title'    => 'Home',
            ]
        );

        $pageHomeSub = \App\Models\Page::create(
            [
                'ref'           => 'home_sub',
                'menu_id'       => $pageHome->id,
                'seo_id'        => $seo->id,
                'menu_title'    => 'Home Sub',
                'page_title'    => 'Home Sub',
            ]
        );

        $seo->fill([
            'fr' => [
                'title'         => 'Titre du site',
                'description'   => 'Description',
            ]
        ]);
        $seo->save();

        $pageHome->fill([
            'fr' => [
                'menu_title'   => 'Accueil',
                'page_title'   => 'Accueil',
            ]
        ]);
        $pageHome->save();

        $pageHomeSub->fill([
            'fr' => [
                'menu_title'   => 'Accueil Sub',
                'page_title'   => 'Accueil Sub',
            ]
        ]);
        $pageHomeSub->save();

        $blockHome = \App\Models\Block::create([
            'type'      => \App\Models\Block::BLOCK_TYPES['string'],
            'title'     => 'block title',
            'page_id'   => $pageHome->id,
        ]);

        $blockHome->fill([
            'fr' => [
                'title'   => 'titre du block',
            ]
        ]);
        $blockHome->save();

        $blockHome = \App\Models\Block::create([
            'type'      => \App\Models\Block::BLOCK_TYPES['text'],
            'body'      => 'block desc',
            'page_id'   => $pageHome->id,
        ]);

        $blockHome->fill([
            'fr' => [
                'body'   => 'desc du block',
            ]
        ]);
        $blockHome->save();
    }
}
