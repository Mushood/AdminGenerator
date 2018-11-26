<?php

namespace Sleekcube\AdminGenerator\Generator;

use Illuminate\Support\Facades\Schema;
use Sleekcube\AdminGenerator\Helpers\Pluraliser;

class FactoryGenerator extends CreateGenerator
{
    const SNIPPET_GENERIC = [
        'topBoilerplate' => "/topBoilerplate.txt",
        'dependencies' => '/dependencies.txt',
        'openingTag' => '/openingTag.txt',
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
        $boilerplate = $this->replaceBySnippet('test', self::SNIPPET_GENERIC['topBoilerplate']);

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
        $boilerplate = $this->replaceBySnippet('test', self::SNIPPET_GENERIC['dependencies']);

        /*if (isset($translationConfigs['isTranslation']) && $translationConfigs['isTranslation']) {
            $boilerplate .= "use Illuminate\Support\Facades\App; \n";
            $boilerplate .= "use App\Models\\". ucwords($this->table) . "Translation; \n\n";
        }*/

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
        $boilerplate = $this->replaceBySnippet('test', self::SNIPPET_GENERIC['openingTag']);

        fwrite($file, $boilerplate);

        return $file;
    }

    /**
     * @param $file
     * @return mixed
     */
    public function customize($file)
    {
        $indexes = $this->getIndexes($this->table);
        $columns = Schema::getColumnListing(Pluraliser::getPlural($this->table));
        $boilerplate = "";

        foreach ($columns as $key => $column) {
            if (!in_array($column,self::UNFILLABLE) && $this->table != $this->getOwnerModelName($column)) {
                if (in_array($column, $indexes) && !in_array($column,self::IGNORED_INDEXES)) {
                    $boilerplate .= "'" . $this->table . "." . $this->getOwnerModelName($column) . "' => '";
                } else {
                    $boilerplate .= "'" . $column . "' => $" . "faker->";
                }

                $boilerplate = $this->addFake($boilerplate, $column, $this->table);

                $boilerplate .= ", \n";
            }
        }

        fwrite($file, $boilerplate);

        return $file;
    }

    private function addFake($boilerplate, $column, $table)
    {
        switch (Schema::getColumnType(Pluraliser::getPlural($table), $column)) {
            case "string":
                $boilerplate .= "text(15)";
                break;

            case "text":
                $boilerplate .= "text(50)";
                break;

            case "boolean":
                $boilerplate .= "boolean";
                break;

            case "datetime":
                $boilerplate .= "datetime";
                break;
        }

        return $boilerplate;
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
            $boilerplate = $this->replaceBySnippet('test', $fileCustom);

            fwrite($file, $boilerplate);
        }

        return $file;
    }

    protected function writeClassClosingTag($file)
    {
        $boilerplate = "\n\t];\n});";

        fwrite($file, $boilerplate);

        return $file;
    }
}
