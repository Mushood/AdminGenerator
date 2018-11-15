<?php

namespace App\Generator;

class RouteJSGenerator extends ModifyGenerator
{
    const MENU_FILE = 'menu.js';
    const LAYOUT_FILE = 'layout.js';
    const KEY_INJECTION = '//INJECT_ROUTESJS_HERE';
    const SNIPPETS = [
        'linkmenu' => "/linkmenu.txt",
        'index' => "/index.txt",
        'create' => "/create.txt",
        'show' => "/show.txt",
    ];

    /**
     * Generate JS Routes
     */
    public function generate()
    {
        $destination = $this->getMenuFilePath();
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION, $content);
        $content = $this->addLinkMenu($content);
        file_put_contents($destination, $content);

        $destination = $this->getLayoutFilePath();
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION, $content);
        $content = $this->addRoutes($content);
        file_put_contents($destination, $content);
    }

    public function generateTranslation()
    {
        $this->generate();
    }

    /**
     * @param $content
     * @return string
     */
    private function addLinkMenu($content)
    {
        $content[0] .= $this->replaceBySnippet('routejs', self::SNIPPETS['linkmenu']);

        return $content[0] . self::KEY_INJECTION . $content[1] ;
    }

    /**
     * @param $content
     * @return string
     */
    private function addRoutes($content)
    {
        $content = $this->addIndexRoute($content);
        $content = $this->addCreateRoute($content);
        $content = $this->addShowRoute($content);

        return $content[0] . self::KEY_INJECTION . $content[1] ;
    }

    /**
     * @param $content
     * @return mixed
     */
    private function addIndexRoute($content)
    {
        $content[0] .= $this->replaceBySnippet('routejs', self::SNIPPETS['index']);

        return $content;
    }

    /**
     * @param $content
     * @return mixed
     */
    private function addCreateRoute($content)
    {
        $content[0] .= $this->replaceBySnippet('routejs', self::SNIPPETS['create']);

        return $content;
    }

    /**
     * @param $content
     * @return mixed
     */
    private function addShowRoute($content)
    {
        $content[0] .= $this->replaceBySnippet('routejs', self::SNIPPETS['show']);

        return $content;
    }

    /**
     * Get File Path
     *
     * @return string
     */
    public function getMenuFilePath()
    {
        return base_path() . $this->directory .  self::MENU_FILE;
    }

    /**
     * @return string
     */
    public function getLayoutFilePath()
    {
        return base_path() . $this->directory . 'router/' . self::LAYOUT_FILE;
    }
}
