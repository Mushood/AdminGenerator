<?php

namespace Sleekcube\AdminGenerator\Generator;

use Sleekcube\AdminGenerator\Helpers\Pluraliser;
use Illuminate\Support\Facades\Schema;

/**
 * Class TransformerGenerator
 * @package App\Generator
 */
class TransformerGenerator extends CreateGenerator
{
    const KEY_INJECTION = "INJECT_CODE_HERE";
    const SNIPPETS = [
        'topBoilerplate' => "/topBoilerplate.txt",
        'dependencies' => '/dependencies.txt',
        'openingTag' => '/openingTag.txt',
        'transform' => '/transform.txt',
        'conform' => '/conform.txt',
    ];

    /**
     * Opening tags and namespace
     *
     * @param $file
     * @return mixed
     */
    protected function writeTopBoilerplate($file, $translationConfigs = [])
    {
        $boilerplate = $this->replaceBySnippet('transformer', self::SNIPPETS['topBoilerplate']);

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
        $boilerplate = $this->replaceBySnippet('transformer', self::SNIPPETS['dependencies']);

        if (isset($translationConfigs['isTranslation']) && $translationConfigs['isTranslation']) {
            $boilerplate .= "use Illuminate\Support\Facades\App; \n\n";
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
        $boilerplate = $this->replaceBySnippet('transformer', self::SNIPPETS['openingTag']);

        fwrite($file, $boilerplate);

        return $file;
    }

    /**
     * @param $file
     * @return mixed
     */
    protected function customize($file)
    {
        $file = $this->addTransformFunction($file);
        $file = $this->addConformFunction($file);

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
        $file = $this->addTransformFunction($file, true);
        $file = $this->addConformFunction($file, true);

        return $file;
    }

    /**
     * @param $file
     * @return mixed
     */
    public function addTransformFunction($file, $translation = false)
    {
        $boilerplate = $this->replaceBySnippet('transformer', self::SNIPPETS['transform']);

        $boilerplate = $this->addTransformRule($boilerplate, $this->table);

        if ($translation) {
            $boilerplate = $this->addTransformRule($boilerplate, $this->table . "_translation");
            $boilerplate .= "\t\t\t'slug' => $" . $this->table . "->hasTranslation(App::getLocale()) ? $";
            $boilerplate .= $this->table . "->translate(App::getLocale())->slug : $";
            $boilerplate .= $this->table . "->translate(config('app.fallback_locale'))->slug, \n";
            $boilerplate .= "\t\t\t'locale'    => App::getLocale(), \n";
        } else {
            $boilerplate .= "\t\t\t'slug' => $" . $this->table . "->slug, \n";
        }

        $boilerplate .= "\t\t];\n}\n";

        fwrite($file, $boilerplate);

        return $file;
    }

    private function addTransformRule($boilerplate, $table)
    {
        $indexes = $this->getIndexes($table);
        $columns = Schema::getColumnListing(Pluraliser::getPlural($table));

        foreach ($columns as $key => $column) {
            if (in_array($column, $indexes) && !in_array($column,self::IGNORED_INDEXES) && $this->table != $this->getOwnerModelName($column)) {
                $boilerplate .= "\t\t\t'" . $this->getOwnerModelName($column) .
                    "' => (new \App\Transformers\\" . ucwords($this->getOwnerModelName($column)) ."Transformer)->transform($" .
                    $this->table . "->" . $this->getOwnerModelName($column) . "), \n";
            } else {
                if (!in_array($column,self::UNFILLABLE) && $this->table != $this->getOwnerModelName($column)) {
                    $boilerplate .= "\t\t\t'" . $column . "' => $" . $this->table . "->" . $column . ", \n";
                }
            }
        }

        return $boilerplate;
    }

    /**
     * @param $file
     * @return mixed
     */
    public function addConformFunction($file, $translation = false)
    {
        $boilerplate = $this->replaceBySnippet('transformer', self::SNIPPETS['conform']);
        $boilerplate = $this->addConformRule($boilerplate, $this->table);

        if ($translation) {
            $boilerplate = $this->addConformRule($boilerplate, $this->table . "_translation");
        }

        $boilerplate .= "\t\t];\n}\n";

        fwrite($file, $boilerplate);

        return $file;
    }

    private function addConformRule($boilerplate, $table)
    {
        $indexes = $this->getIndexes($table);
        $columns = Schema::getColumnListing(Pluraliser::getPlural($table));
        foreach ($columns as $key => $column) {
            if (Schema::getConnection()->getDoctrineColumn(Pluraliser::getPlural($table), $column)->getNotnull()) {
                if (in_array($column, $indexes) && !in_array($column, self::IGNORED_INDEXES) && $this->table != $this->getOwnerModelName($column)) {
                    $boilerplate .= "\t\t\t'" . $column . "' => $" . $this->getOwnerModelName($column) . "->id, \n";

                    $boilerplate = $this->loadRelationBySlug($boilerplate, $column);
                } else {
                    if (!in_array($column, self::UNFILLABLE) && $this->table != $this->getOwnerModelName($column)) {
                        $boilerplate .= "\t\t\t'" . $column . "' => $" . "validatedData['" . $column . "'], \n";
                    }
                }
            } else {
                // #TODO Care about nullale foreign key
                if (!in_array($column, self::UNFILLABLE) && $this->table != $this->getOwnerModelName($column)) {
                    $boilerplate .= "\t\t\t'" . $column . "' => isset($" . "validatedData['" . $column . "']) ? $" . "validatedData['" . $column . "'] : null , \n";
                }

            }
        }

        return $boilerplate;
    }

    private function loadRelationBySlug($boilerplate, $column)
    {
        $content = explode("//" . self::KEY_INJECTION, $boilerplate);

        $content[0] .= "$" . $this->getOwnerModelName($column) ." = \App\Models\\" . ucwords($this->getOwnerModelName($column))
            . "::where('slug', $" . "validatedData['" . $this->getOwnerModelName($column) . "'])->first();";

        return $content[0] . $content[1];
    }
}
