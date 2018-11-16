<?php

namespace Sleekcube\AdminGenerator\Generator;

use Sleekcube\AdminGenerator\Helpers\Pluraliser;
use Illuminate\Support\Facades\Schema;

class RequestGenerator extends CreateGenerator
{
    const SNIPPETS = [
        'topBoilerplate' => "/topBoilerplate.txt",
        'dependencies' => '/dependencies.txt',
        'openingTag' => '/openingTag.txt',
        'authorize' => '/authorize.txt',
        'rules' => '/rules.txt',
    ];

    /**
     * Opening tags and namespace
     *
     * @param $file
     * @return mixed
     */
    protected function writeTopBoilerplate($file, $translationConfigs = [])
    {
        $boilerplate = $this->replaceBySnippet('request', self::SNIPPETS['topBoilerplate']);

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
        $boilerplate = $this->replaceBySnippet('request', self::SNIPPETS['dependencies']);

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
        $boilerplate = $this->replaceBySnippet('request', self::SNIPPETS['openingTag']);

        fwrite($file, $boilerplate);

        return $file;
    }

    /**
     * @param $file
     * @return mixed
     */
    protected function customize($file, $translation = false)
    {
        $file = $this->addAuthorizeFunction($file);
        $file = $this->addRulesFunction($file, $translation);

        return $file;
    }

    /**
     * @param $file
     * @return mixed
     */
    protected function customizeBelong($file)
    {
        $file = $this->customize($file);

        return $file;
    }

    protected function customizeTranslation($file)
    {
        $file = $this->customize($file, true);

        return $file;
    }

    /**
     * @param $file
     * @return mixed
     */
    private function addAuthorizeFunction($file)
    {
        $boilerplate = $this->replaceBySnippet('request', self::SNIPPETS['authorize']);

        fwrite($file, $boilerplate);

        return $file;
    }

    /**
     * @param $file
     * @return mixed
     */
    private function addRulesFunction($file, $translation = false)
    {
        $boilerplate = $this->replaceBySnippet('request', self::SNIPPETS['rules']);

        $boilerplate = $this->addColumnRule($boilerplate, $this->table);

        if ($translation) {
            $boilerplate = $this->addColumnRule($boilerplate, $this->table . "_translation");
        }

        $boilerplate .= "];\n}\n";

        fwrite($file, $boilerplate);

        return $file;
    }

    private function addColumnRule($boilerplate, $table)
    {
        $indexes = $this->getIndexes($table);
        $columns = Schema::getColumnListing(Pluraliser::getPlural($table));

        foreach ($columns as $key => $column) {
            if (!in_array($column,self::UNFILLABLE) && $this->table != $this->getOwnerModelName($column)) {
                if (in_array($column, $indexes) && !in_array($column,self::IGNORED_INDEXES)) {
                    $boilerplate .= "'" . $this->table . "." . $this->getOwnerModelName($column) . "' => '";
                } else {
                    $boilerplate .= "'" . $this->table . "." . $column . "' => '";
                }

                $boilerplate = $this->addRule($boilerplate, $column, $table);

                $boilerplate .= "', ";
            }
        }

        return $boilerplate;
    }

    /**
     * @param $boilerplate
     * @param $column
     * @return string
     */
    private function addRule($boilerplate, $column, $table)
    {
        if (Schema::getConnection()->getDoctrineColumn(Pluraliser::getPlural($table), $column)->getNotnull()){
            $boilerplate .= "Required";

            switch (Schema::getColumnType(Pluraliser::getPlural($table), $column)) {
                case "string":
                    $boilerplate .= "|Min:0|Max:255|String";
                    break;

                case "boolean":
                    $boilerplate .= "|Boolean";
                    break;

                case "datetime":
                    $boilerplate .= "|Date";
                    break;
            }

        } else {
            $boilerplate .= "Nullable";
        }

        $indexes = $this->getIndexes($table);
        if (in_array($column, $indexes) && !in_array($column,self::IGNORED_INDEXES)) {
            $boilerplate .= "|exists:" . Pluraliser::getPlural($this->getOwnerModelName($column)) .",slug";
        }

        return $boilerplate;
    }
}
