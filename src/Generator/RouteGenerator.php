<?php

namespace App\Generator;

class RouteGenerator extends ModifyGenerator
{
    const SNIPPETS = [
        'routes' => "/routes.txt",
        'routes_trans' =>"/routesTrans.txt",
    ];

    const KEY_INJECTION = '//INJECT_ROUTES_HERE';

    /**
     * Generate Routes to api.php File
     */
    public function generate($translation = false)
    {
        $destination = $this->getFilePath();
        $content = file_get_contents($destination);
        $content = explode(self::KEY_INJECTION, $content);

        if ($translation) {
            $content = $this->addRoutesTranslation($content);
        } else {
            $content = $this->addRoutes($content);
        }

        file_put_contents($destination, $content);

        exec('php php-cs-fixer-v2.phar fix ' . $this->getFilePath());
    }

    public function generateTranslation()
    {
        $this->generate(true);
    }


    /**
     * @param $content
     * @return string
     */
    private function addRoutes($content)
    {
        $content[0] .= $this->replaceBySnippet('route', self::SNIPPETS['routes']);

        return $content[0] . "\n" . self::KEY_INJECTION . $content[1] ;
    }

    private function addRoutesTranslation($content)
    {
        $content[0] .= $this->replaceBySnippet('route', self::SNIPPETS['routes_trans']);

        return $content[0] . "\n" . self::KEY_INJECTION . $content[1] ;
    }

    /**
     * Get File Path
     *
     * @return string
     */
    public function getFilePath()
    {
        return base_path() . $this->directory .  $this->extension;
    }
}
