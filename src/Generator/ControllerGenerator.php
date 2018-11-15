<?php

namespace App\Generator;

use App\Helpers\StringManipulator;

class ControllerGenerator extends CreateGenerator
{
    const SNIPPET_GENERIC = [
        'topBoilerplate' => "/topBoilerplate.txt",
        'dependencies' => '/dependencies.txt',
        'openingTag' => '/openingTag.txt',
    ];

    const SNIPPET_CUSTOMIZE = [
        'constructor' => '/constructor.txt',
        'index' => '/index.txt',
        'store' => '/store.txt',
        'update' => '/update.txt',
        'show' => '/show.txt',
        'destroy' => '/destroy.txt',
    ];

    const SNIPPET_TRANS = [
        'constructor' => '/constructor.txt',
        'index' => '/indexTrans.txt',
        'store' => '/storeTrans.txt',
        'update' => '/updateTrans.txt',
        'show' => '/showTrans.txt',
        'destroy' => '/destroyTrans.txt',
    ];

    /**
     * Opening tags and namespace
     *
     * @param $file
     * @return mixed
     */
    protected function writeTopBoilerplate($file, $translationConfigs = [])
    {
        $boilerplate = $this->replaceBySnippet('controller', self::SNIPPET_GENERIC['topBoilerplate']);

        fwrite($file, $boilerplate);

        return $file;
    }

    /**
     * import any dependencies used in the class
     *
     * @param $file
     * @return mixed
     */
    protected function writeDependencies($file, $translationConfigs = [])
    {
        $boilerplate = $this->replaceBySnippet('controller', self::SNIPPET_GENERIC['dependencies']);

        if (isset($translationConfigs['isTranslation']) && $translationConfigs['isTranslation']) {
            $boilerplate .= "use Illuminate\Support\Facades\App; \n";
            $boilerplate .= "use App\Models\\". ucwords($this->table) . "Translation; \n\n";
        }

        fwrite($file, $boilerplate);

        return $file;
    }

    /**
     * open class syntax
     *
     * @param $file
     * @return mixed
     */
    protected function writeClassOpeningTag($file, $translationConfigs = [])
    {
        $boilerplate = $this->replaceBySnippet('controller', self::SNIPPET_GENERIC['openingTag']);

        fwrite($file, $boilerplate);

        return $file;
    }

    /**
     * @param $file
     * @return mixed
     */
    public function customize($file)
    {
        foreach (self::SNIPPET_CUSTOMIZE as $fileCustom) {
            $boilerplate = $this->replaceBySnippet('controller', $fileCustom);

            fwrite($file, $boilerplate);
        }

        return $file;
    }

    /**
     * @param $file
     * @return mixed
     */
    protected function customizeBelong($file)
    {
        return $this->customize($file);
    }

    protected function customizeTranslation($file)
    {
        foreach (self::SNIPPET_TRANS as $fileCustom) {
            $boilerplate = $this->replaceBySnippet('controller', $fileCustom);

            fwrite($file, $boilerplate);
        }

        return $file;
    }
}
